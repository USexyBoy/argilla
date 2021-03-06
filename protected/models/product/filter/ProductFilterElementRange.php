<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */

class ProductFilterElementRange extends ProductFilterElement
{
  public $minValue = null;
  public $maxValue = null;

  public function prepareAvailableValues($value, $filtered)
  {
    if( !empty($value) )
    {
      $this->setRange($value);
      $value = $this->getRangeAvailableValue($value);
    }

    return $value;
  }

  public function addPropertyCondition(CDbCriteria $criteria)
  {
    if( $this->selected )
    {
      $range = explode("-", $this->selected);
      $criteria->addBetweenCondition($this->id, $range[0], $range[1]);
    }
  }

  public function getParameterCondition()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);

    $param = explode("-", $this->selected);
    if( !empty($param) && !empty($param[0]) && !empty($param[1]) )
      $criteria->addBetweenCondition('(value + 0)', $param[0], $param[1]);
    else if( !empty($param[0]) )
      $criteria->compare('(value + 0)', '>'.$param[0]);
    else if( !empty($param[1]) )
      $criteria->compare('(value + 0)', '<'.$param[1]);

    return $criteria;
  }

  /**
   * @param CDbCriteria $criteria
   * @return CDbCriteria
   */
  public function buildPropertyAmountCriteria(CDbCriteria $criteria)
  {
    $criteria->distinct = true;

    $select = "(CASE \n";

    foreach($this->itemLabels as $key => $value)
    {
      $min = explode("-", $key)[0];
      $max = explode("-", $key)[1];
      $select .= "WHEN {$this->id} >= {$min} AND {$this->id} <= {$max} THEN '{$key}'\n";
    }

    $select .= "ELSE 0 END)";

    // Пока единственное рабочее решение, GROUP по {$this->id} работает не корректно
    $criteria->select = "{$select} AS {$this->id}_key_for_group \n, {$select} AS {$this->id} \n , COUNT(t.id) AS count";
    $criteria->group  = "{$this->id}_key_for_group";

    return $criteria;
  }

  protected function getRangeAvailableValue($item)
  {
    foreach($this->itemLabels as $id => $itemLabel)
    {
      if( intval($item) >= explode("-", $id)[0] && intval($item) <= explode("-", $id)[1] )
        return $id;
    }

    return $item;
  }

  protected function sortItems($items)
  {
    if( empty($items) )
      return $items;

    uasort($items, function($a, $b){
      return strnatcmp($a->id, $b->id);
    });

    return $items;
  }

  protected function setRange($value)
  {
    $value = intval($value);

    if( $this->minValue === null )
      $this->minValue = $value;

    if( $this->maxValue === null )
      $this->maxValue = $value;

    $this->minValue = $this->minValue <= $value ? $this->minValue : $value;
    $this->maxValue = $this->maxValue >= $value ? $this->maxValue : $value;
  }
}