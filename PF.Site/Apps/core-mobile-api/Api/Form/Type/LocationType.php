<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 2/7/18
 * Time: 3:45 PM
 */

namespace Apps\Core_MobileApi\Api\Form\Type;


use Apps\Core_MobileApi\Api\Form\TransformerInterface;

class LocationType extends GeneralType implements TransformerInterface
{

    protected $componentName = "Location";

    public function getMetaValueFormat()
    {
        return "['address', 'lat', 'lng']";
    }

    public function getAvailableAttributes()
    {
        return [
            'label',
            'description',
            'value',
            'returnKeyType',
            'address',
            'lat',
            'lng'
        ];
    }

    public function isValid()
    {
        if (!parent::isValid()) {
            return false;
        }
        $values = $this->getValue();
        if (!$this->isRequiredField() && ($values == null || empty($values['address']))) {
            return true;
        }
        $isValid = true;

        if (!is_array($values)) {
            $isValid = false;
        } else if (!isset($values['lat']) || !isset($values['lng']) || !isset($values['address'])) {
            $isValid = false;
        }

        return $isValid;
    }

    public function transform($value)
    {
        if (is_array($value) && !empty($this->getAttr('use_transform'))) {
            return [
                'location_lat' => isset($value['lat']) ? $value['lat'] : 0,
                'location_lng' => isset($value['lng']) ? $value['lng'] : 0,
                'location'     => isset($value['address']) ? $value['address'] : ''
            ];
        }
        return $value;
    }

    public function reverseTransform($data)
    {
        $localNameField = !empty($this->getAttr('location_name_field')) ? $this->getAttr('location_name_field') : 'location';
        $localCoordinateField = !empty($this->getAttr('location_coordinate_field')) ? $this->getAttr('location_coordinate_field') : 'coordinate';
        $locationName = isset($data[$localNameField]) ? $data[$localNameField] : '';
        $locationCoordinate = isset($data[$localCoordinateField]) ? $data[$localCoordinateField] : [];

        return [
            'address' => $locationName,
            'lat'     => isset($locationCoordinate['latitude']) ? $locationCoordinate['latitude'] : 0,
            'lng'     => isset($locationCoordinate['longitude']) ? $locationCoordinate['longitude'] : 0,
        ];
    }

}