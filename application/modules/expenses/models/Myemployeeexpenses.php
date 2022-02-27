<?php
/*********************************************************************************
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/
/**
 *
 * @model Expenses Model
 * @author sagarsoft
 *
 */
class Expenses_Model_Myemployeeexpenses extends Zend_Db_Table_Abstract
{
	//echo "expensesmodel";exit;
	protected $_name = 'expenses';
	protected $_primary = 'id';

	/**
	 * This will fetch all the client details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 * @param string $a
	 * @param string $b
	 * @param string $c
	 * @param string $d
	 *
	 * @return array
	 */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		
		  if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
            if(count($searchValues) >0)
            {
                foreach($searchValues as $key => $val)
                {    
                    if($key == 'expense_date') 
					{
						$searchQuery .= " date(".$key.") = '".  sapp_Global::change_date($val,'database')."' AND ";	
					} 				
                    else{
							
						if($key=='status')
						{
							$searchQuery .= " ex. ".$key." like '%".$val."%' AND ";
						}else
						{
							$searchQuery .= " ".$key." like '%".$val."%' AND ";
						}
						
					}
                        
                    $searchArray[$key] = $val;
                }
                $searchQuery = rtrim($searchQuery," AND");
            }
        }
			
		$objName = 'myemployeeexpenses';
		
		$tableFields = array(
					'action'=>'Action',
					'userfullname'=>'Employee',
					'expense_name' => 'Expense',
					'trip_name' => 'Trip',
					'expense_category_name' => 'Category',
					'expense_date' => 'Expense date',
					'currencyname' => 'Currency',
					'expense_amount' => 'Amount',
					'status' => 'Status',
		);

		$tablecontent = $this->getEmployeeSubmittedExpenseData($sort, $by, $pageNo, $perPage,$searchQuery);
		
		//echo "<pre>";print_r($tablecontent);exit;

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			'menuName' => 'Employee Expenses',
			  'search_filters' => array(
                          
                            'expense_date'=>array('type'=>'datepicker'),
                            
                        ),
			);
			//echo "<pre>";print_r($dataTmp);exit;
			return $dataTmp;
	}
	
	public function getEmployeeSubmittedExpenseData($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
		 	$login_UserId = $auth->getStorage()->read()->id;

		}
		if($loginUserId=='')
		{
			$loginUserId = $login_UserId;
		}	  
		//$loginUserId=2; 
				$where = "ex.isactive = 1 AND ex.status!='saved' AND ex.manager_id = ".$loginUserId." ";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$employeeadvancesData = 
		  $this->select()
		->setIntegrityCheck(false)
		->from(array('ex' => 'expenses'),array('ex.*','expense_date'=>'DATE_FORMAT(expense_date,"'.DATEFORMAT_MYSQL.'")'))
	    ->joinInner(array('u'=>'main_users'), "u.id = ex.createdby and
	   u.isactive = 1",array('userfullname'=>'u.userfullname')) 
	   ->joinInner(array('cat'=>'expense_categories'), "cat.id = ex.category_id and
	   cat.isactive = 1",array('expense_category_name'=>'cat.expense_category_name')) 
	   ->joinInner(array('cur'=>'main_currency'), "cur.id = ex.expense_currency_id and
	   cur.isactive = 1",array('currencyname'=>'cur.currencyname')) 
	   ->joinLeft(array('t'=>'expense_trips'), "t.id = ex.trip_id and
	   t.isactive = 1 ",array('trip_name'=>
	   't.trip_name'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		return $employeeadvancesData;
	
	}

	/**
	 * This will fetch all the active client details.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $expensesData
	 */
	public function getExpensesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$where = "e.isactive = 1 and e.createdby = ".$loginUserId;

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$expensesData = $this->select()
		->setIntegrityCheck(false)
		->from(array('e'=>'expenses'),array('e.*','expense_date'=>'DATE_FORMAT(expense_date,"'.DATEFORMAT_MYSQL.'")'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		//echo "<pre>";print_r($expensesData);exit;
		return $expensesData; 
		
		
		/* 
		$expensesData = $this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>'expenses'),array('c.expense_name','c.expense_date','c.expense_amount','c.status'))						
						->where('c.isactive = 1 AND c.employee_id='.$emp_id);
						//SELECT CONCAT(UPPER(SUBSTR(col, 0, 1)), SUBSTR(col, 1))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		//echo "<pre>";print_r($expensesData);exit;
		return $expensesData; */
	}

	/**
	 * This method will save or update the client details based on the client id.
	 *
	 * @param array $data
	 * @param string $where
	 */
	public function saveOrUpdateExpensesData($data, $where){
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}
	
	/**
	 * This method is used to fetch client details based on id.
	 * 
	 * @param number $id
	 */
	public function getExpenseDetailsById($id)
	{

			
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = "ex.isactive = 1";
		
		 $select =
	 $this->select()
		->setIntegrityCheck(false)
		->from(array('ex' => 'expenses'))
	    ->joinLeft(array('tp'=>'tm_projects'), "tp.id = ex.project_id",array('project_name'=>'tp.project_name'))
		->joinLeft(array('c'=>'tm_clients'), "c.id = ex.client_id",array('client_name'=>'c.client_name'))
		->joinInner(array('mc'=>'main_currency'), "mc.id = ex.expense_currency_id",array('currencycode'=>'mc.currencycode'))
		->joinInner(array('ep'=>'expense_payment_methods'), "ep.id = ex.expense_payment_id",array('payment_method_name'=>'ep.payment_method_name'))
		->joinInner(array('ec'=>'expense_categories'), "ec.id = ex.category_id",array('expense_category_name'=>'ec.expense_category_name'))
		->joinLeft(array('er'=>'expense_receipts'), "er.expense_id = ex.id and er.isactive = 1",array('receipt_name'=>'er.receipt_name','receipt_filename'=>'er.receipt_filename'))
		->joinLeft(array('et'=>'expense_trips'), "et.id = ex.trip_id",array('trip_name'=>'et.trip_name','from_date'=>'et.from_date','to_date'=>'et.to_date'))
		->joinLeft(array('advs'=>'expense_advacne_summary'), "advs.employee_id = ex.createdby",array('total'=>'advs.total'))
		->where('ex.isactive = 1 AND ex.id='.$id.' ') 
		->order("ex.id DESC");
		//->limit($limit,$offset); 
		return $this->fetchAll($select)->toArray();
	}
	
	/**
	 * This method is used to check weather the client is associated in any project or not.
	 * 
	 * @param unknown_type $clientId
	 */
	public function checkExpensesAndTrips($expenseId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from expenses where trip_id != '' AND id = ".$expenseId." AND isactive = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
		
	} 
	public function getExpenses($expense_id=0,$limit,$offset)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'ex.isactive=1 and ex.status!="approved"  and ex.status!="submitted" ';
		if($expense_id>0)
		{
			$where .= ' and ex.id!='.$expense_id;
		}
			
		$expenseData = $this->select()
		->setIntegrityCheck(false)
		->from(array('ex' => 'expenses'))
	    ->joinInner(array('ec'=>'expense_categories'), "ec.id = ex.category_id and
	    ec.isactive = 1",array('expense_category_name'=>'ec.expense_category_name'))
		->where($where)
		->limit($limit,$offset)
		;
		
		return $this->fetchAll($expenseData)->toArray();
	}
	public function getExpensesCount($expense_id=0)
	{
		$where = '';
		if($expense_id>0)
		{
			$where = ' and e.id!='.$expense_id;
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$count_query = "select count(id) cnt from expenses e where e.isactive = 1 and e.status!='approved'  and e.status!='submitted'".$where;
		$count_result = $db->query($count_query);
		$count_row = $count_result->fetch();
		return $count_row['cnt'];  
	}
	
	
	/* public function saveOrUpdateHistory($data, $where){		

		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	} */

	
	public function getCurrencyList()
	{
	  $geographygroupData = $this->select()
                                    ->setIntegrityCheck(false)	
                                    
                                    ->from(array('c'=>'main_currency'),array('c.id','currency'=>'c.currencyname','currencycode'=>'c.currencycode'))
                                     ->where('c.isactive = 1')
						   ->order('c.currencyname');
      return $this->fetchAll($geographygroupData)->toArray();
	}
	
	
}