<?php
namespace Octo\Admin\Controller;

use b8\Form;
use b8\Image;
use b8\Http\Upload;
use Octo\Store;
use Octo\Admin\Controller;
use Octo\Admin\Form as FormElement;
use Octo\Admin\Menu;
use Octo\Utilities\StringUtilities;
use Octo\Model\File;

class MediaController extends Controller
{
    /**
     * Return the menu nodes required for this controller
     *
     * @param Menu $menu
     * @return void
     * @author James Inman
     */
    public static function registerMenus(Menu $menu)
    {
        $media = $menu->addRoot('Media', '/media')->setIcon('picture-o');
        $media->addChild(new Menu\Item('Upload', '/media/add'));

        $images = new Menu\Item('Manage Images', '/media/manage/images');
        $media->addChild($images);
        $edit = new Menu\Item('Edit Image', '/media/edit/images', true);
        $images->addChild($edit);
        $files = new Menu\Item('Manage Files', '/media/manage/files');
        $media->addChild($files);

        $media->addChild(new Menu\Item('Search (Autocomplete)', '/media/autocomplete', true));
        $edit = new Menu\Item('Edit File', '/media/edit/files', true);
        $files->addChild($edit);
    }

    /**
     * @var \Octo\Store\FileStore
     */
    protected $fileStore;
    /**
     * @var \Octo\Store\CategoryStore
     */
    protected $categoryStore;

    /**
     * Setup initial menu
     *
     * @return void
     * @author James Inman
     */
    public function init()
    {
        $this->setTitle('Media');
        $this->addBreadcrumb('Media', '/media/upload');

        $this->fileStore = Store::get('File');
        $this->categoryStore = Store::get('Category');
    }

    /**
     * Upload files
     *
     * @return void
     * @author James Inman
     */
    public function add()
    {
        if ($this->request->getMethod() == 'POST') {
            $upload = new Upload('file');
            $info = $upload->getFileInfo();

            if ($file = $this->fileStore->getById(strtolower($info['hash']))) {
                $data = array_merge($file->getDataArray(), array('url' => $file->getUrl()));
                print json_encode($data);
                exit;
            }

            $file = new File;
            $file->setId(strtolower($info['hash']));
            $file->setFilename(strtolower($info['basename']));
            $file->setTitle(strtolower($info['basename']));
            $file->setExtension(strtolower($info['extension']));
            $file->setMimeType($info['type']);
            $file->setSize($info['size']);
            $file->setCreatedDate(new \DateTime);
            $file->setUpdatedDate(new \DateTime);
            $file->setUserId($this->currentUser->getId());

            switch ($info['type']) {
                case 'image/jpeg':
                    $file->setScope('images');
                    break;
                case 'image/png':
                    $file->setScope('images');
                    break;
                case 'image/gif':
                    $file->setScope('images');
                    break;
                case 'image/pjpeg':
                    $file->setScope('images');
                    break;
                default:
                    $file->setScope('files');
                    break;
            }

            $categories = $this->categoryStore->getByScope($file->getScope());
            if (isset($categories[0])) {
                $file->setCategoryId($categories[0]->getId());
            }

            try {
                $uploadDirectory = APP_PATH . 'public/uploads/';
                $upload->copyTo($uploadDirectory . $info['hash'] . '.' . $info['extension']);
                $file = $this->fileStore->saveByInsert($file);

                $url = '/uploads/' . $info['hash'] . '.' . $info['extension'];
                $data = array_merge($file->getDataArray(), array('url' => $url));
                print json_encode($data);
                exit;
            } catch (\Exception $ex) {
                print json_encode(array('error' => true));
            }
        }
    }

    public function edit($scope, $fileId)
    {
        $file = $this->fileStore->getById($fileId);
        $this->view->scope_name = StringUtilities::singularize(ucwords($scope));

        $this->setTitle('Edit File: ' . $file->getTitle());
        $this->addBreadcrumb(ucwords($scope), '/media/' . $scope . '/' . $fileId);
        $this->addBreadcrumb($file->getTitle(), '/media/edit/' . $scope . '/' . $fileId);

        if ($this->request->getMethod() == 'POST') {
            $values = array_merge($this->getParams(), array('id' => $fileId));
            $form = $this->fileForm($values, $scope, 'edit');

            if ($form->validate()) {
                try {
                    $file->setValues($this->getParams());
                    $file = $this->fileStore->save($file);
                    $this->successMessage($file->getTitle() . ' was edited successfully.', true);

                    header('Location: /' . $this->config->get('site.admin_uri') . '/media/manage/' . $scope);
                } catch (Exception $e) {
                    $this->errorMessage('There was an error editing the file. Please try again.');
                }
            } else {
                $this->errorMessage('There was an error editing the file. Please try again.');
            }
        }

        $this->view->form = $this->fileForm($file->getDataArray(), $scope)->render();

        $imageFiles = ['jpg', 'jpeg', 'gif', 'png'];
        if (in_array($file->getExtension(), $imageFiles)) {
            $this->view->image = $file->getUrl();
        }
    }

    protected function fileForm($values, $scope)
    {
        $form = new FormElement();
        $form->setMethod('POST');

        $form->setAction('/' . $this->config->get('site.admin_uri') . '/media/edit/' . $scope . '/' . $values['id']);

        $form->setClass('smart-form');

        $fieldset = new Form\FieldSet('fieldset');
        $form->addField($fieldset);

        $field = new Form\Element\Text('title');
        $field->setRequired(true);
        $field->setLabel('Title');
        $fieldset->addField($field);

        $field = new Form\Element\Text('filename');
        $field->setRequired(true);
        $field->setLabel('File Name');
        $fieldset->addField($field);

        $field = new Form\Element\Submit();
        $field->setValue('Save File');
        $field->setClass('btn-success');
        $fieldset->addField($field);

        $form->setValues($values);
        return $form;
    }

    /**
     * @param $scope Scope of files to view
     */
    public function manage($scope)
    {
        $scope_name = ucwords($scope);

        $this->setTitle($scope_name);
        $this->addBreadcrumb($scope_name, '/' . $scope);

        $this->view->scope = $scope;
        $this->view->scope_name = $scope_name;
        $this->view->files = $this->fileStore->getAllForScope($scope);

        if ($scope == 'images') {
            $this->view->gallery = true;
        }
    }

    /**
     * @param $scope Scope of files to view
     */
    public function autocomplete($scope)
    {
        $scope_name = ucwords($scope);
        $files = $this->fileStore->search($scope, $this->getParam('q', ''));

        $rtn = ['results' => [], 'more' => false];

        foreach ($files as $file) {
            $rtn['results'][] = ['id' => $file->getId(), 'text' => $file->getTitle()];
        }

        die(json_encode($rtn));
    }

    /**
     * @param $scope Scope of file to delete
     * @param $fileId ID of file to delete
     */
    public function delete($scope, $fileId)
    {
        $file = $this->fileStore->getById($fileId);
        @unlink($file->getPath());
        $this->fileStore->delete($file);

        $this->successMessage($file->getTitle() . ' was deleted successfully.', true);
        header('Location: /' . $this->config->get('site.admin_uri') . '/media/manage/' . $scope);
    }

    /**
     * @param $fileId
     * @param int $width
     * @param int $height
     */
    public function render($fileId, $width = 160, $height = 160)
    {
        $file = $this->fileStore->getById($fileId);

        Image::$sourcePath = APP_PATH . '/public/uploads/';
        $image = new Image($file->getId() . '.' . $file->getExtension());
        $output = $image->render($width, $height);

        $this->response->setHeader('Content-Type', 'image/jpeg');
        $this->response->setContent($output);
        $this->response->disableLayout();
        $this->response->flush();
        print $this->response->getContent();
        exit;
    }

    /**
     * Return an AJAX list of all images
     *
     * @param $scope
     * @return string JSON
     */
    public function ajax($scope)
    {
        $files = $this->fileStore->getAllForScope($scope);
        File::$sleepable = array('id', 'url', 'title');
        foreach ($files as &$item) {
            $imageData = getimagesize($item->getPath());
            $item = $item->toArray(1);
            $item['width'] = $imageData[0];
            $item['height'] = $imageData[1];
        }
        print json_encode($files);
        exit;
    }
}
