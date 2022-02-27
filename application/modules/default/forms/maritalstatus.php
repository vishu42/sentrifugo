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

class Default_Form_maritalstatus extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'maritalstatus/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'maritalstatus');


        $id = new Zend_Form_Element_Hidden('id');
		
		$maritalcode = new Zend_Form_Element_Text('maritalcode');
        $maritalcode->setAttrib('maxLength', 20);
        $maritalcode->addFilter(new Zend_Filter_StringTrim());
        $maritalcode->setRequired(true);
        $maritalcode->addValidator('NotEmpty', false, array('messages' => 'Please enter marital code.'));  
        $maritalcode->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid marital code.'
								 )
							 )
						 )
					 ));
		$maritalcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_maritalstatus',
                                                        'field'=>'maritalcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $maritalcode->getValidator('Db_NoRecordExists')->setMessage('Marital code already exists.'); 	
		
		$maritalstatusname = new Zend_Form_Element_Text('maritalstatusname');
        $maritalstatusname->setAttrib('maxLength', 20);
        $maritalstatusname->addFilter(new Zend_Filter_StringTrim());
        $maritalstatusname->setRequired(true);
        $maritalstatusname->addValidator('NotEmpty', false, array('messages' => 'Please enter marital status.'));  
		$maritalstatusname->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_maritalstatus',
                                                        'field'=>'maritalstatusname',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $maritalstatusname->getValidator('Db_NoRecordExists')->setMessage('Marital status already exists.'); 	
        $maritalstatusname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid marital status.'
								 )
							 )
						 )
					 ));	
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'maritalstatus/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "''";;
		 

		 $this->addElements(array($id,$maritalcode,$maritalstatusname,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}