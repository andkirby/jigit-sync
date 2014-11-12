<?php
namespace Lib\Design;

/**
 * Class Pager
 *
 * @package Lib\Design
 */
class Pager extends Renderer
{
    /**
     * Count item per page (default 6)
     *
     * @var int
     */
    protected $countPerPage = 6;

    /**
     * Visible page numbers (default 4)
     */
    protected $visiblePageNumber = 4;

    /**
     * Current page
     *
     * @var integer
     */
    protected $_currentPage;

    /**
     * Total page count
     *
     * @var integer
     */
    protected $_totalPageCount;

    /**
     * Item collection
     *
     * @var \Zend_Paginator
     */
    protected $_collection;

    /**
     * Contains page list
     *
     * @var array
     */
    protected $_pageList = array();

    /**
     * Initialization method (which will be called the last)
     */
    public function init()
    {
        $this->_collection->setCurrentPageNumber($this->_getCurrentPage());
        $this->_collection->setItemCountPerPage($this->getCountPerPage());
        $this->_setTotalPageCount($this->_collection->count());
        $this->_buildPageList();
    }

    /**
     * Build page list
     */
    protected function _buildPageList()
    {
        $this->_pageList['Previous'] = $this->_getPreviousPage();
        $this->_pageList['pages']    = $this->_getNumberList();
        $this->_pageList['Next']     = $this->_getNextPage();
    }

    /**
     * Get collection
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Set collection
     *
     * @param mixed $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * Get current page
     *
     * @return mixed
     */
    protected function _getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Set current page
     *
     * @param mixed $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        if (!isset($currentPage)) {
            $this->_currentPage = 1;
        } else {
            $this->_currentPage = $currentPage;
        }
        return $this;
    }

    /**
     * Get total page count
     *
     * @return mixed
     */
    protected function _getTotalPageCount()
    {
        return $this->_totalPageCount;
    }

    /**
     * Set total page count
     *
     * @param $totalPageCount
     * @internal param mixed $totalPageCaunt
     */
    protected function _setTotalPageCount($totalPageCount)
    {
        $this->_totalPageCount = $totalPageCount;
    }

    /**
     * Get page list
     *
     * @return array
     */
    public function getPageList()
    {
        return $this->_pageList;
    }

    /**
     * Get previous page
     *
     * @return int|mixed
     */
    protected function _getPreviousPage()
    {
        return ($this->_getCurrentPage() - 1 >= 1)
            ? ($this->_getCurrentPage() - 1)
            : 1;
    }

    /**
     * Get next page
     *
     * @return mixed
     */
    protected function _getNextPage()
    {
        return ($this->_getCurrentPage() + 1
            <= $this->_getTotalPageCount()) ?
            ($this->_getCurrentPage() + 1) : $this->_getCurrentPage();
    }

    /**
     * Get count per page
     *
     * @return int
     */
    public function getCountPerPage()
    {
        return $this->countPerPage;
    }

    /**
     * Set count per page
     *
     * @param int $countPerPage
     */
    public function setCountPerPage($countPerPage)
    {
        $this->countPerPage = $countPerPage;
    }

    /**
     * Get visible page number
     *
     * @return mixed
     */
    public function getVisiblePageNumber()
    {
        return $this->visiblePageNumber;
    }

    /**
     * Set visible page number
     *
     * @param mixed $visiblePageNumber
     */
    public function setVisiblePageNumber($visiblePageNumber)
    {
        $this->visiblePageNumber = $visiblePageNumber;
    }

    /**
     * Get number list
     */
    protected function _getNumberList()
    {
        $pageList    = array();
        $currentPage = $this->_getCurrentPage();
        $totalPage   = $this->_getTotalPageCount();
        $difference  = $totalPage - ($currentPage + $this->getVisiblePageNumber());
        if ($difference >= 0) {
            for ($i = 0; $i < $this->getVisiblePageNumber(); $i++) {
                $pageList[$currentPage + $i] = $currentPage + $i;
            }
        } else {
            $currentPage += $difference + 1;
            for ($i = 0; $i < $this->getVisiblePageNumber(); $i++) {
                if (($currentPage + $i) <= 0) {
                    continue;
                }
                $pageList[$currentPage + $i] = $currentPage + $i;
            }
        }

        return $pageList;
    }
}
