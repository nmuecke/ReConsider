<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */
class SurveyController extends Zend_Controller_Action
   {

   public function init()
      {
      if( $this->_isLoggedIn() == true )
         {
//         $this->_helper->redirector('index', 'index');
//         $this->_helper->redirector('index', 'disputes');
         }
      $this->userID = Zend_Auth::getInstance()->getIdentity()->id;

      // track url
      $urlHistory = new Extend_Helpers_RefPage();
      $this->previousPage = $urlHistory->getLastVisited();
      $urlHistory->save( $this->_request->getRequestUri() );
      $this->currentPage  = $urlHistory->getLastVisited();

      // make sure that the dispute is resolved
      $disputeDB = new Default_Model_DisputesAndUsersMapper();
      $dispute = $disputeDB->getResolvedDisputes( $this->userID );

      if( count( $dispute ) < 1 )
         {
         $this->_helper->redirector('index', 'disputes');
         }
      }

   public function indexAction()
      {
      // make sure the survey has not yet been taken
      $surveyResDB = new Default_Model_SurveyResultsMapper();
      if( $surveyResDB->hasTakenSurvey( $this->userID ) == true )
         {
         $this->_helper->redirector('index', 'disputes');
         }

      $form = new Default_Form_Survey_Questions();

      $request = $this->getRequest();

      if( $request->isPost() )
         {
         if( $form->isValid( $request->getPost() ) )
            {
            $results = $form->getValues();

            $surveyResDB = new Default_Model_SurveyResultsMapper();
            $surveyQuDB = new Default_Model_SurveyQuestionsMapper();

            foreach( $results as $name => $res )
               {
               $question = $surveyQuDB->findByName( $name );
               if( $question == null )
                  {
                  throw new Exception( "Unable to find matching survey question to the data posted!" );
                  }
               $data = new Default_Model_SurveyResults();
               $data->setQuestionID( $question->getID() )
                    ->setResult( $res )
                    ->setUserID( $this->userID );

               $surveyResDB->save( $data );
               }
            $this->_helper->redirector( 'thank-you', 'survey' );
            }
         }

      $this->view->form = $form;    
      }

    public function thankYouAction()
       {

       }

    protected function _isLoggedIn()
       {
       $auth = Zend_Auth::getInstance();

       if( $auth->hasIdentity() )
          {
          return true;
          }
       return false;
       }

   }





