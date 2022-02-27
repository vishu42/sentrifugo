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

class Default_Form_numberformats extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'numberformats');


        $id = new Zend_Form_Element_Hidden('id');
		
		$numberformattype = new Zend_Form_Element_Text('numberformattype');
        $numberformattype->setAttrib('maxLength', 30);
        $numberformattype->setRequired(true);
        $numberformattype->addValidator('NotEmpty', false, array('messages' => 'Please enter number format type.'));  
		$numberformattype->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=>'/^(\d?\d?\d(,\d\d\d)*|\d+)(\.\d\d)?$/',
							 'messages' => array(
							 'regexNotMatch'=>'Please enter valid number format type.'
								 )
							 )
						 )
					 )); 
		
		$numberformattype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_numberformats',
                                                        'field'=>'numberformattype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $numberformattype->getValidator('Db_NoRecordExists')->setMessage('Number format type already exists.');
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$numberformattype,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}