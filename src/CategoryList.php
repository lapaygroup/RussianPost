<?php
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Providers\Calculation;

class CategoryList
{
    use Singleton;

    private $Calculation = false;
    private $subcategory = true;
    private $description = false;
    private $categoryDelete = [];

    function __construct()
    {
        $this->Calculation = Calculation::getInstance();
    }

    /**
     * @param bool $subcategory
     */
    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;
    }

    /**
     * @param bool $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param array $categoryDelete
     */
    public function setCategoryDelete($categoryDelete)
    {
        $this->categoryDelete = $categoryDelete;
    }

    /**
     * @return bool
     */
    public function getSubcategory()
    {
        return $this->subcategory;
    }

    /**
     * @return bool
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getCategoryDelete()
    {
        return $this->categoryDelete;
    }

    public function parseToArray()
    {
        $categoryList = [];
        $list = $this->Calculation->getCategoryList();
        foreach($list['category'] as $item) {
            //Пропускаем категории, которые нужно пропустить
            if(in_array($item['id'], $this->categoryDelete)) continue;
            $categoryItem = [];

            $categoryItem['id'] = $item['id'];
            $categoryItem['category'] = $item['name'];

            if($this->description && !$this->subcategory) {
                $descriptionList = [];
                $resultDescription = $this->Calculation->getCategoryDescription($categoryItem['id']);
                foreach($resultDescription['category'] as $description) {
                    $descriptionList[$description['id']] = $description['desc'];
                }
            }

            foreach($item['child'] as $childInfo) {
                $categoryItem['subcategory_list'][$childInfo['id']]['id'] = $childInfo['id'];
                $categoryItem['subcategory_list'][$childInfo['id']]['subcategory'] = $childInfo['name'];

                if($this->subcategory) {
                    $objectInfo = $this->Calculation->getObjectInfo($childInfo['id']);
                    if(!empty($objectInfo)) {
                        //Получаем описание категории
                        $categoryItem['subcategory_list'][$childInfo['id']]['description'] = $objectInfo['desc'];

                        //Получаем подкатегории отправления
                        if(!empty($objectInfo['object']) && is_array($objectInfo['object'])) {
                            foreach ($objectInfo['object'] as $objInfo) {
                                $itemInfo = [];
                                $itemInfo['id'] = $objInfo['id'];
                                $itemInfo['name'] = $objInfo['name'];
                                $itemInfo['service_list'] = !empty($objInfo['service']) ? $objInfo['service'] : []
                                $itemInfo['fields'] = $objInfo['params'];
                                $categoryItem['subcategory_list'][$childInfo['id']]['items'][$objInfo['id']] = $itemInfo;
                            }
                        } else {
                            $categoryItem['subcategory_list'][$childInfo['id']]['items'] = false;
                        }
                    }
                }

                if($this->description && !$this->subcategory) {
                    $categoryItem['subcategory_list'][$childInfo['id']]['description'] = $descriptionList[$childInfo['id']];
                }
            }
            $categoryList[] = $categoryItem;
        }

        return $categoryList;
    }
}