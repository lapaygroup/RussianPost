<?php
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Providers\Calculation;

class CategoryList
{
    use Singleton;

    private $Calculation = false;

    function __construct()
    {
        $this->Calculation = Calculation::getInstance();
    }

    public function parseToArray($subcategory=false, $description=false)
    {
        $categoryList = [];
        $list = $this->Calculation->getCategoryList();
        foreach($list['category'] as $item) {
            $categoryItem = [];

            $categoryItem['id'] = $item['id'];
            $categoryItem['category'] = $item['name'];

            if($description && !$subcategory) {
                $descriptionList = [];
                $resultDescription = $this->Calculation->getCategoryDescription($categoryItem['id']);
                foreach($resultDescription['category'] as $description) {
                    $descriptionList[$description['id']] = $description['desc'];
                }
            }

            foreach($item['child'] as $childInfo) {
                $categoryItem['subcategory_list'][$childInfo['id']]['id'] = $childInfo['id'];
                $categoryItem['subcategory_list'][$childInfo['id']]['subcategory'] = $childInfo['name'];

                if($subcategory) {
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
                                $itemInfo['service_list'] = $objInfo['service'];
                                $itemInfo['fields'] = $objInfo['params'];
                                $categoryItem['subcategory_list'][$childInfo['id']]['items'][$objInfo['id']] = $itemInfo;
                            }
                        } else {
                            $categoryItem['subcategory_list'][$childInfo['id']]['items'] = false;
                        }
                    }
                }

                if($description && !$subcategory) {
                    $categoryItem['subcategory_list'][$childInfo['id']]['description'] = $descriptionList[$childInfo['id']];
                }
            }
            $categoryList[] = $categoryItem;
        }

        return $categoryList;
    }
}