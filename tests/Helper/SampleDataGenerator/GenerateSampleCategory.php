<?php
declare(strict_types=1);

namespace Fc2blog\Tests\Helper\SampleDataGenerator;

use Faker\Factory;
use Fc2blog\Model\CategoriesModel;
use InvalidArgumentException;
use RuntimeException;

class GenerateSampleCategory
{
  /**
   * @param string $blog_id
   * @param $parent_id
   * @param int $num
   * @return array created result list
   */
  public function generateSampleCategory(string $blog_id, $parent_id = 0, int $num = 10): array
  {
    $categories_model = new CategoriesModel();
    $result = [];
    $faker = $this->getFaker();

    while ($num--) {
      $insert_data = [
        'blog_id' => $blog_id,
        'parent_id' => (string)$parent_id,
        'name' => $faker->word(),
        'category_order' => static::getRandomKey($categories_model->getOrderList()),
      ];

      $errors = $categories_model->validate($insert_data, $data);

      if (count($errors) > 0) {
        if ("同一階層に同じ名前が存在しています" === ($errors['name'] ?? false)) {
          continue;
        }

        throw new InvalidArgumentException("validate error:" . print_r($errors, true));
      }

      $data['blog_id'] = $blog_id;
      $id = $categories_model->addNode($data, 'blog_id=?', array($blog_id));

      if ($id == false) {
        throw new RuntimeException("insert error:" . print_r($data, true));
      }

      $result[] = [
        'id' => $id,
        'parent_id' => $data['parent_id'],
        'name' => $data['name'],
      ];
    }

    return $result;
  }

  /**
   * @param string $blog_id
   * @return array
   */
  public function getCategoryList(string $blog_id): array
  {
    $categories_model = new CategoriesModel();
    return $categories_model->getList($blog_id);
  }

  public function syncRemoveAllCategories(string $blog_id)
  {
    $categories_model = new CategoriesModel();
    $list = static::getCategoryList($blog_id);

    foreach ($list as $row) {
      if ($row['parent_id'] === 0 && $row['name'] === "未分類") {
        continue; // don't delete root category.
      }
      if (1 !== $categories_model->deleteByIdAndBlogId($row['id'], $row['blog_id'])) {
        throw new RuntimeException("delete failed, " . print_r($row, true));
      }
    }
  }

  public function getFaker()
  {
    return Factory::create('ja_JP');
  }

  static public function getRandomValue(array $array)
  {
    $_array = $array;
    shuffle($_array);
    return $_array[0];
  }

  static public function getRandomKey(array $array)
  {
    $_array = $array;
    $_array = array_keys($_array);
    shuffle($_array);
    return $_array[0];
  }
}
