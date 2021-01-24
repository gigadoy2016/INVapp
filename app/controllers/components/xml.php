<?php

  class XMLComponent extends Object {

    var $layout = 'xml';
    var $enabled = true;

    // This is so you can specify which nodes you do not want
    // included in the XML.
    var $excludedNodes = array();

    var $version = '1.0';
    var $encoding = 'UTF-8';
    var $rowNameNode = '';

    var $__domDocument = NULL;

    function startup(&$controller) {
      if (!$this->enabled || !( strtolower(
$controller->params['webservices'] ) == strtolower( 'xml' ))) {
        return true;

      }
      $this->__domDocument = new DOMDocument( $this->version,
$this->encoding );

    }

    function buildXMLBody( $nodeName, $nodeValue ) {
      $retval = NULL;

      /**
       * Using the 3rd argument because evidently if the needle
argument
       * is a numeric 0, it will find it in the array somehow.  Not
exactly sure
       * why that's happening...
       **/
      if( !( in_array( $nodeName, $this->excludedNodes, TRUE ))) {

        if( is_numeric( $nodeName )) {
          // If I don't do this, the DOM throws an exception.
          // Evidently, it doesn't like numbers as node names
          $nodeName = $this->rowNameNode;

        }
        if( is_array( $nodeValue )) {
          $domElement = $this->__domDocument->createElement( $nodeName
);
          foreach( $nodeValue as $name => $value ) {
            if( $node = $this->buildXMLBody( $name, $value )) {
              $domElement->appendChild( $node );

            }
          }
        } else {
          $domElement = $this->__domDocument->createElement( $nodeName
);
          $domElement->appendChild(
$this->__domDocument->createTextNode( htmlentities( $nodeValue )));

        }
        $retval = $domElement;

      }
      return $retval;

    }

    function dataToXML( $dataArray, $rootNodeName = 'root',
$rowNameNode = 'row' ) {

      $retval = NULL;
      $this->rowNameNode = $rowNameNode;

      $rootNode = $this->__domDocument->createElement( $rootNodeName );

      foreach( $dataArray as $nodeName => $nodeValue ) {
        if( $domElement = $this->buildXMLBody( $nodeName, $nodeValue ))
{
          $rootNode->appendChild( $domElement );

        }
      }
      $this->__domDocument->appendChild( $rootNode );

      $retval = $this->__domDocument->saveXML( NULL, LIBXML_NOEMPTYTAG
);

      return $retval;

    }
  }

?> 