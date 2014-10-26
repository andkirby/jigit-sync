<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 10/26/2014
 * Time: 3:20 PM
 */

namespace JigitTest\Response;

use JigitTest\Object;

class Collection extends Object
{
    /**
     *
     *
     * @var string
     */
    protected $_dataItemsKey = 'items';

    /**
     * Constructor. Set default data
     *
     * @param string $dataItemsKey
     */
    public function __construct($dataItemsKey = 'items')
    {
        $this->_data = array(
            'expand'        => 'schema,names',
            'startAt'       => 0,
            'maxResults'    => 20,
            'total'         => 0,
            $dataItemsKey => array(),
        );
        $this->_dataItemsKey = $dataItemsKey;
    }

    /**
     * Get recursively data array
     *
     * @return array
     */
    public function toArray()
    {
        $data = $this->_data;
        /** @var \JigitTest\Object $item */
        foreach ($this->_data[$this->_dataItemsKey] as $key => $item) {
            $data[$key] = $item instanceof Object ? $item->toArray() : $item;
        }
        return $data;
    }

    /**
     * Convert data to json
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Set expand
     *
     * @param string $expand
     * @return $this
     */
    public function setExpand($expand)
    {
        $this->_data['expand'] = $expand;
        return $this;
    }

    /**
     * Get expand
     *
     * @return string
     */
    public function getExpand()
    {
        return $this->_data['expand'];
    }

    /**
     * Set startAt
     *
     * @param integer $startAt
     * @return $this
     */
    public function setStartAt($startAt)
    {
        $this->_data['startAt'] = $startAt;
        return $this;
    }

    /**
     * Get startAt
     *
     * @return integer
     */
    public function getStartAt()
    {
        return $this->_data['startAt'];
    }

    /**
     * Set maxResults
     *
     * @param integer $maxResults
     * @return $this
     */
    public function setMaxResults($maxResults)
    {
        $this->_data['maxResults'] = $maxResults;
        return $this;
    }

    /**
     * Get maxResults
     *
     * @return integer
     */
    public function getMaxResults()
    {
        return $this->_data['maxResults'];
    }

    /**
     * Set total
     *
     * @param integer $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->_data['total'] = $total;
        return $this;
    }

    /**
     * Increment total
     *
     * @return $this
     */
    public function incrementTotal()
    {
        $this->_data['total']++;
        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->_data['total'];
    }

    /**
     * Set items
     *
     * @param array $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->_data[$this->_dataItemsKey] = $items;
        return $this;
    }

    /**
     * Set items
     *
     * @param Object $item
     * @return $this
     */
    public function addItem($item)
    {
        $this->_data[$this->_dataItemsKey][] = $item;
        $this->incrementTotal();
        return $this;
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_data[$this->_dataItemsKey];
    }
}
