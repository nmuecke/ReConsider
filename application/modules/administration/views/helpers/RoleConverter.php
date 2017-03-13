<?php
class Administration_View_Helper_RoleConverter extends Zend_View_Helper_Abstract
   {

   public function RoleConverter( $role )
      {
      switch( $role )
         {
         case DOCTOR:
            return 'Doctor';
            break;
         case PATIENT:
            return 'Pattient';
            break;
         default:
         }
      return null;
      }
   }
