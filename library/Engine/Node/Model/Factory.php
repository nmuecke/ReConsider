<?php

class Engine_Node_Model_Factory
   {
   private static $_instance;

   private final function __construct()
      {
      // abstract constructor
      }

   public static function getInstance()
      {
      if( null === self::$_instance )
         {
         // create instance
         self::$_instance = new self();
         }

      return self::$_instance;
      }

   public final function createNode( $type )
      {
      // is the type a valid class name and if so is it a node
      if( class_exists( $type ) )
         {
         $node = new $type();
         if( is_subclass_of( $node, "Engine_Node_Model_Abstract" ) )
            {
            return $node;
            }
         }

      // can the type be mapped to an existing node type
      $type = self::dectectNodeType( $type );


      if( $type != null && class_exists( $type ) )
         {
         $node = new $type();
         if( is_subclass_of( $node, "Engine_Node_Model_Abstract" ) )
            {
            return $node;
            }

         }

      // no valid node found
      unset( $node );
//      throw new Exception( "Unable to create new node of type " . $type );
      }

   protected function dectectNodeType( $type )
      {
      switch( strtolower( $type ) )
         {
         case "a":
         case "argument":
            $type = "Engine_Node_Model_Argument";
            break;
         case "c":
         case "conclusion":
            $type = "Engine_Node_Model_Conclusion";
            break;
         case "d":
         case "decision":
            #TODO implement node type
            $type = "Engine_Node_Model_Argument";
            break;
         case "l":
         case "leaf":
            $type = "Engine_Node_Model_Leaf";
            break;
         case "r":
         case "root":
            $type = "Engine_Node_Model_Root";
            break;
         default;
            $type = null;
         }
      return $type;
      }
    
   }
