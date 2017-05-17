<?php
namespace Bx
{
  /**
   * Перечисление результатов запроса к Highload-блоку.
   */
  class ResultHighload extends Result
  {
    /**
     * Сущность блока.
     * @var string
     */
    protected $block;

    /**
     * Создает экземпляр класса.
     * @param string $block Сущность блока.
     */
    public function __construct($block)
    {
      $this->block = $block;
    }

    /**
     * Получает очередной элемент.
     * @return array Элемент.
     */
    protected function fetch()
    {
      return $this->result->Fetch();
    }

    /**
     * Загружает данные из блока.
     * @return CDBResult Результат запроса к блоку.
     */
    protected function result()
    {
      $filter = $this->filter;
      $select = array('*');

      $params = array(
        'filter' => $filter,
        'select' => $select
      );

      if ($this->offset) $params['offset'] = $this->offset;
      if ($this->limit) $params['limit'] = $this->limit;
      if ($this->order) $params['order'] = $this->order;

      $block = $this->block;
      return $block::getList($params);
    }
  }
}