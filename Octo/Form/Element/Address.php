<?php
namespace Octo\Form\Element;

use b8\Form\Element;
use b8\Form\FieldSet;
use b8\Form\Element\Text;

class Address extends FieldSet
{
    public function __construct($name = null)
    {
        parent::__construct($name);

        $address1 = $name . '[address1]';
        $address2 = $name . '[address2]';
        $town = $name . '[town]';
        $postcode = 'postcode';

        if ($name != 'address') {
            $postcode = $name . '[' . $postcode . ']';
        }

        $address1 = Text::create($address1, 'Address 1', true);
        $address2 = Text::create($address2, 'Address 2', false);
        $town = Text::create($town, 'Town', true);
        $postcode = Text::create($postcode, 'Postcode', true);

        $this->addField($address1);
        $this->addField($address2);
        $this->addField($town);
        $this->addField($postcode);
    }

    public function setRequired($required)
    {
        if ($required) {
            foreach ($this->children as &$child) {
                $child->setRequired(true);
            }

            $this->children[$this->getName() . '[address2]']->setRequired(false);
        } else {
            foreach ($this->children as &$child) {
                $child->setRequired(false);
            }
        }
    }

    public function setParent(Element $parent)
    {
        parent::setParent($parent);

        foreach ($this->getChildren() as &$child) {
            $child->setViewLoader($this->getViewLoader());
        }
    }
}
