<?php

namespace App\Tests\Controller;

use App\Controller\SearchParamsController;
use PHPUnit\Framework\TestCase;
use App\Entity\SearchParams;

class SearchParamsControllerTest extends TestCase
{

    private $_searchParamsController;

//    protected function setUp(): void
//    {
//        $this->_searchParamsController = new SearchParamsController();
//    }

//    public function testDelete()
//    {
//        $this->_searchParamsController = new SearchParamsController();
//        $searchParamsId = 11;
//        $this->assertEquals(
//            $searchParamsId,
//            json_decode(
//                $this->_searchParamsController->delete($searchParamsId)->getContent()
//            )
//        );
//
//    }

//    public function testCreate()
//    {
//
//    }
//
//    public function testGetAll()
//    {
//
//    }
//
//    public function testGetSearchParams()
//    {
//        $this->_searchParamsController = new SearchParamsController();
//        $this->assertIsObject($this->_searchParamsController->getSearchParams(11));
//    }
//
//    public function testUpdate()
//    {
//
//    }

    public function testIsTrue()
    {
        $this->_searchParamsController = new SearchParamsController();
        $this->assertEquals(true, $this->_searchParamsController->isTrue(true));
    }
}
