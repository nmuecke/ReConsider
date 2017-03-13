<?php


class Administration_DataController extends Zend_Controller_Action
   {
   public function init()
      {
      /* Initialize action controller here */

      $urlHistory = new Extend_Helpers_RefPage();
      $urlHistory->save( $this->_request->getRequestUri() );
      $auth = Zend_Auth::getInstance();

       if( !$auth->hasIdentity() )
          {
          $this->_helper->redirector('request-denied', 'auth', 'default' );
          }

       // get the user's info
       $dbAdapter = new Default_Model_AuthMapper();
       $user = new Default_Model_Auth();
       $dbAdapter->find( $auth->getIdentity()->username, $user );

       if( null === ( $user = $dbAdapter->findByUsername( $auth->getIdentity()->username, $user ) ) )
          {
          throw new Exception( "ERROR! Unable to validate your user credentuals ".
                               "whilest initiating a new dispute." );
          }

      $this->validationID = $auth->getIdentity()->id;

      }

   public function indexAction()
      {

      }

  public function surveyResultsDataOnlyAction()
      {
      $this->surveyResultsAction();
      }

  public function surveyResultsAction()
      {
      $tableStruct = new Extend_Struct_Model_Table(); 
      $surveyDB = new Default_Model_SurveyResultsMapper();
      $questionsDB = new Default_Model_SurveyQuestionsMapper();
     
      // get and load the survey questions
      $res = $questionsDB->findAll();
      $tableStruct = $questionsDB->formatSurveyQuestions( $res, $tableStruct ); 

      // get and load the survey results 
      $res = $surveyDB->getResults(  );
      $tableStruct = $surveyDB->formatSurvey( $res, $tableStruct ); 


      $this->_getUserInfo( $tableStruct );

      $this->view->data = $tableStruct;
      }

  private function _getUserInfo( Extend_Struct_Model_Table $table )
      {
      $authDB = new Default_Model_AuthUsersMapper();
     
      $rows = $table->getRows();


    
      foreach( $rows as $row )
        {
        $res = $authDB->findActiveUserData( $row->getID() );
        $gender = $group = $role = null;
        if( count( $res ) > 0 )
           {
           $gender = $res[0]['gender'];
           $group  = $res[0]['uni'];
           $role   = $res[0]['role'];
           }
        else if( $row->getID() == 'heading')
           {
           $gender = 'Gender';
           $group  = 'Group';
           $role   = 'Role';
           }

        $row->insertCol( 1, $group  );
        $row->insertCol( 2, $gender );
        $row->insertCol( 3, $role   );
        }
      }

  public function disputeDataAction()
      {
      $disputeDB = new Default_Model_DisputesAndUsersMapper();

      $disputes = $disputeDB->getDisputes();

      $this->view->disputes = $disputes;
      }

  public function disputeResultsAction()
      {
      $tableStruct = new Extend_Struct_Model_Table();

      $this->view->data = $tableStruct;
      }

  private function compileData()
      {
      }
}
