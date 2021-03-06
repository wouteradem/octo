<?php

namespace Octo\Form\Element;

use b8\View;
use b8\Form\Element\Upload;

class ImageUpload extends Upload
{
    /**
     * @var string ID of current image
     */
    protected $imageId;
    /**
     * @var string Whether to accept multiple elements
     */
    protected $multiple = '';

    /**
     * @return string
     */
    public function render($viewFile = 'ImageUpload')
    {
        return parent::render($viewFile);
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);
        $view->type = 'file';
        $view->imageId = $this->imageId;
        $view->multiple = $this->multiple;
    }

    /**
     * @param $url
     */
    public function setImageId($url)
    {
        $this->imageId = $url;
    }

    /**
     * Sets the "multiple" property on the element to true
     */
    public function setMultiple()
    {
        $this->multiple = 'multiple';
    }


    /**
     * Validate the upload
     *
     * @return bool
     */
    public function validate()
    {
        $multipleUploads = false;
        if (strval($this->multiple) != '') {
            $field = str_replace('[]', '', $this->name);
            if (isset($_FILES[$field]['name'][0]) && strlen($_FILES[$field]['name'][0]) > 0) {
                $multipleUploads = true;
            }
        }

        if (!$this->checkValue($multipleUploads)) {
            return false;
        }

        $validator = $this->getValidator();
        $this->callValidator($validator);

        if ($this->customError) {
            return false;
        }

        return true;
    }

    /**
     * Check whether the correct value has been entered based on multiple or single upload
     *
     * @param bool $multipleUploads
     * @return bool
     */
    protected function checkValue($multipleUploads)
    {
        if ($this->getRequired() && !$multipleUploads && (is_null($this->value) || $this->value == '')) {
            $this->error = $this->getLabel() . ' is required.';
            return false;
        }

        if ($this->getPattern() && !preg_match('/' . $this->getPattern() . '/', $this->value)) {
            $this->error = 'Invalid value entered.';
            return false;
        }

        return true;
    }

    /**
     * Call the custom validator
     *
     * @param $validator
     * @return bool
     */
    protected function callValidator($validator)
    {
        if (is_callable($validator)) {
            try {
                call_user_func_array($validator, array($this->value));
            } catch (\Exception $ex) {
                $this->error = $ex->getMessage();
                return false;
            }
        }
    }
}
