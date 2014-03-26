<?php
namespace Octo\Shop\Event;

use b8\Config;
use b8\Form;
use Octo\Event\Listener;
use Octo\Event\Manager;
use Octo\Shop\Model\ItemFile;
use Octo\Store;

class ProductEditListener extends Listener
{
    protected $productStore;

    public function registerListeners(Manager $manager)
    {
        $manager->registerListener('shopMediaList', array($this, 'setupMediaDisplay'));
        $manager->registerListener('shopMediaEditForm', array($this, 'setupEditForm'));
        $manager->registerListener('shopMediaEditPostSave', array($this, 'editFormProcessed'));
        $manager->registerListener('shopFileFormFields', array($this, 'setupFileForm'));
        $manager->registerListener('shopDeleteFile', array($this, 'deleteFileProcessed'));
        $manager->registerListener('shopUpload', array($this, 'setupUpload'));
        $manager->registerListener('shopBeforeUploadProcessed', array($this, 'uploadProcessed'));
        $manager->registerListener('shopFileSaved', array($this, 'fileSaved'));
    }

    public function setupMediaDisplay(&$instance)
    {
        $product = end($instance->getRequest()->getPathParts());

        $this->productStore = Store::get('Item');
        $product = $this->productStore->getBySlug($product);

        if ($product) {
            $instance->popBreadcrumb();
            $instance->popBreadcrumb();
            $instance->addBreadcrumb('Products', '/product');
            $instance->addBreadcrumb($product->getTitle(), '/product/edit/' . $product->getId());
            $instance->addBreadcrumb('Images', '/media/manage/shop/' . $product->getSlug());
            $instance->view->title = $product->getTitle();
            $instance->view->thumbnail = true;
            $instance->view->reorder = true;
            $instance->view->reorderSaveUrl = '/' . Config::getInstance()->get('site.admin_uri') . '/product/update-image-positions?item_id=' . $product->getId();
            $instance->view->queryStringAppend = '?item_id=' . $product->getId();
        }

        return true;
    }

    public function setupEditForm(&$instance) {
        $this->productStore = Store::get('Item');
        $product = $this->productStore->getById($instance->getParam('item_id'));

        if ($product) {
            $file = $instance->popBreadcrumb();
            $instance->popBreadcrumb();
            $instance->popBreadcrumb();
            $instance->addBreadcrumb('Products', '/product');
            $instance->addBreadcrumb($product->getTitle(), '/product/edit/' . $product->getId());
            $instance->addBreadcrumb('Images', '/media/manage/shop/' . $product->getSlug());
            $instance->addBreadcrumb($file['title'], $file['link'] . '?item_id=' . $product->getId());
            $instance->view->title = $product->getTitle();
        }

        return true;
    }

    public function setupUpload(&$instance) {
        $this->productStore = Store::get('Item');
        $product = $this->productStore->getById($instance->getParam('item_id'));

        if ($product) {
            $file = $instance->popBreadcrumb();
            $instance->addBreadcrumb('Products', '/product');
            $instance->addBreadcrumb($product->getTitle(), '/product/edit/' . $product->getId());
            $instance->addBreadcrumb('Images', '/media/manage/shop/' . $product->getSlug());
            $instance->addBreadcrumb('Upload', '#');
        }

        return true;
    }

    public function editFormProcessed(&$instance)
    {
        $this->productStore = Store::get('Item');
        $product = $this->productStore->getById($instance->getParam('item_id'));
        header('Location: /' . Config::getInstance()->get('site.admin_uri') . '/media/manage/shop/' . $product->getSlug());
        exit;
    }

    public function uploadProcessed(&$file)
    {
        $file->setScope('shop');
        $file->setId(md5(strtolower($file->getId() . $file->getScope())));
    }

    public function fileSaved($file)
    {
        $this->productStore = Store::get('Item');
        $product = $this->productStore->getById($_GET['item_id']);

        $itemFile = new ItemFile();
        $itemFile->setFileId($file->getId());
        $itemFile->setItemId($_GET['item_id']);
        Store::get('ItemFile')->save($itemFile);
    }

    public function deleteFileProcessed(&$instance)
    {
        $this->productStore = Store::get('Item');
        $product = $this->productStore->getById($instance->getParam('item_id'));
        header('Location: /' . Config::getInstance()->get('site.admin_uri') . '/media/manage/shop/' . $product->getSlug());
        exit;
    }

    public function setupFileForm(&$formParts)
    {
        list($form, $values) = $formParts;
        $form->setAction($form->getAction() . '?item_id=' . $_GET['item_id']);
        $formParts = [$form, $values];

        return true;
    }
}