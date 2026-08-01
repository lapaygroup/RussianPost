<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\Calculation;

class CategoryList
{
    private readonly Calculation $calculation;
    private bool $subcategory = true;
    private bool $description = false;
    private array $categoryDelete = [];

    public function __construct(Psr18Transport $httpTransport)
    {
        $this->calculation = new Calculation($httpTransport);
    }

    /**
     * @param bool $subcategory
     */
    public function setSubcategory(bool $subcategory): void
    {
        $this->subcategory = $subcategory;
    }

    /**
     * @param bool $description
     */
    public function setDescription(bool $description): void
    {
        $this->description = $description;
    }

    /**
     * @param array $categoryDelete
     */
    public function setCategoryDelete(array $categoryDelete): void
    {
        $this->categoryDelete = $categoryDelete;
    }

    /**
     * @return bool
     */
    public function getSubcategory(): bool
    {
        return $this->subcategory;
    }

    /**
     * @return bool
     */
    public function getDescription(): bool
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getCategoryDelete(): array
    {
        return $this->categoryDelete;
    }

    public function parseToArray(): array
    {
        $categoryList = [];
        $list = $this->calculation->getCategoryList();
        $categories = $list['category'] ?? [];
        if (!is_array($categories)) {
            throw new \UnexpectedValueException('Поле category в ответе тарификатора должно быть массивом');
        }

        foreach ($categories as $item) {
            //Пропускаем категории, которые нужно пропустить
            if (in_array($item['id'], $this->categoryDelete, true)) continue;
            $categoryItem = [];
            $descriptionList = [];

            $categoryItem['id'] = $item['id'];
            $categoryItem['category'] = $item['name'];

            if ($this->description && !$this->subcategory) {
                $resultDescription = $this->calculation->getCategoryDescription((int) $categoryItem['id']);
                foreach ($resultDescription['category'] ?? [] as $description) {
                    $descriptionList[$description['id']] = $description['desc'];
                }
            }

            foreach ($item['child'] ?? [] as $childInfo) {
                $categoryItem['subcategory_list'][$childInfo['id']]['id'] = $childInfo['id'];
                $categoryItem['subcategory_list'][$childInfo['id']]['subcategory'] = $childInfo['name'];

                if ($this->subcategory) {
                    $objectInfo = $this->calculation->getObjectInfo((int) $childInfo['id']);
                    if (!empty($objectInfo)) {
                        //Получаем описание категории
                        $categoryItem['subcategory_list'][$childInfo['id']]['description'] = $objectInfo['desc'] ?? null;

                        //Получаем подкатегории отправления
                        if (!empty($objectInfo['object']) && is_array($objectInfo['object'])) {
                            foreach ($objectInfo['object'] as $objInfo) {
                                $itemInfo = [];
                                $itemInfo['id'] = $objInfo['id'];
                                $itemInfo['name'] = $objInfo['name'];
                                $itemInfo['service_list'] = !empty($objInfo['service']) ? $objInfo['service'] : [];
                                $itemInfo['fields'] = $objInfo['params'] ?? [];
                                $categoryItem['subcategory_list'][$childInfo['id']]['items'][$objInfo['id']] = $itemInfo;
                            }
                        } else {
                            $categoryItem['subcategory_list'][$childInfo['id']]['items'] = false;
                        }
                    }
                }

                if ($this->description && !$this->subcategory) {
                    $categoryItem['subcategory_list'][$childInfo['id']]['description'] = $descriptionList[$childInfo['id']] ?? null;
                }
            }
            $categoryList[] = $categoryItem;
        }

        return $categoryList;
    }
}
