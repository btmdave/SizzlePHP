<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Helper;
use Sizzle\Helper\Xml\ArrayToXml;

/**
 * Xml
 *
 * Helper class for XML manipulation
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Xml {

    /**
     * arrayToXml
     * @param string $node_name
     * @param array  $arr
     */
    public function arrayToXml($node_name, $arr=array())
    {
        return ArrayToXml::createXML($node_name, $arr);
    }
    
    /**
     * DEPRECIATED! 
     * @see arrayToXml
     * @uses Xml_ArrayToXml
     * Recursive function used to convert an array to XML
     * This does not support attributes
     * 
     *
     * @param array
     * @param null
     * @param string
     * @return xml
     */
    function toXml($data, $structure = null, $basenode = 'xml'){
    
        if ($structure === null)
        {
            $structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
        }
    
        if(!empty($data))
        {
            foreach ($data as $key => $value)
            {
                if( is_numeric($key))
                {
                    $key = 'item';
                }
    
                // replace non-alphanumeric
                $key = preg_replace('/[^a-z_\-0-9]/i', '', $key);
    
                // if there is another array found recrusively call this function
                if (is_array($value) || is_object($value))
                {
                    $node = $structure->addChild($key);
    
                    // recrusive call.
                    $this->toXml($value, $node, $key);
                }
    
                else
                {
                    // add single node.
                    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");
    
                    $structure->addChild($key, $value);
                }
            }
        }
    
        // Using DOMDocument to format xml output - will validate xml (TODO: Unit test this)
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($structure->asXML());
    
        return $dom->saveXML();
    
    }
    
    
    /**
     * Converts an XML string to an array
     *
     * @param string
     * @param boolean
     * @return $this->domNodeToArray(@param documentElement)
     */
	public function xmlStringToArray($xmlstr, $root = false) {
	    
	  if($root) {
	      
	      $simpleXmlElementObject = simplexml_load_string($xmlstr);
	      
	      if ($simpleXmlElementObject == null) {
	          return false;
	      }
	      
	      $simpleXmlRootElementName = $simpleXmlElementObject->getName();
	      
	      //Convert the XML element to a PHP array
	      $array = $this->xmlElementObjectToArray($simpleXmlElementObject);
	      
	      if (($array != null) && (sizeof($array) > 0)) {
	          return $array;
	      }
	      
	      return false;  
	      
	  }  
	  
	  $doc = new \DOMDocument();
	  libxml_use_internal_errors(true);
	  
	  if(!$doc->loadXML($xmlstr)){
	      return false;
	  } 
	  
	       return $this->domnodeToArray($doc->documentElement);
	  
	}
	
	/**
	 * Recursive function that accepts a dom node and 
	 * returns an as an array
	 *
	 * @param DOMDocument node
	 * @return array
	 */
	private function domnodeToArray($node) {
	
	  $output = array();
	  switch ($node->nodeType) {
	
	    case XML_CDATA_SECTION_NODE:
	    case XML_TEXT_NODE:
	      $output = trim($node->textContent);
	    break;
	
	    case XML_ELEMENT_NODE:
	      for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
	         
	        $child = $node->childNodes->item($i);
	        $v = $this->domnodeToArray($child);
	        
	        if(isset($child->tagName)) {
	          $t = $child->tagName;
	     
	          if(!isset($output[$t])) {
	            $output[$t] = array();
	          }
	          
	          $output[$t][] = $v;
	        }
	        elseif($v || $v === '0') {
	          $output = (string) $v;
	        }
	      }
	      
	      if(is_array($output)) {
	        if($node->attributes->length) {
	          $a = array();
	          foreach($node->attributes as $attrName => $attrNode) {
	            $a[$attrName] = (string) $attrNode->value;
	          }
	          $output['@attributes'] = $a;
	        }
	        foreach ($output as $t => $v) {
	          if(is_array($v) && count($v)==1 && $t!='@attributes') {
	            $output[$t] = $v[0];
	          }
	        }
	      }
	    break;
	  }
	  return $output;
	}

	/**
	 * xmlStringToJson
	 * @param string $xmlStringContents
	 */
    public function xmlStringToJson($xmlStringContents) {

            $simpleXmlElementObject = simplexml_load_string($xmlStringContents);
            
            if ($simpleXmlElementObject == null) {
                return false;
            }
        
            $simpleXmlRootElementName = $simpleXmlElementObject->getName();
    
            //Convert the XML element to a PHP array
            $array = $this->xmlElementObjectToArray($simpleXmlElementObject);
    
            if (($array != null) && (sizeof($array) > 0)) {
                return json_encode($array);
            } 

        return false;
    } 
    
    /**
     * Defines the max number of recursions that may occure for a given
     * XML node.  Used within private method xmlElementObjectToArray
     *
     * @var string|int
     */
    private $_max_recursion_depth = 25;
    
    
    /**
     * Recursive function iterating through XML element object and converting to an array
     * to later be used for converting to json.
     *
     * @param SimpleXMLElementObject
     * @param int
     * @return mixed
     */
    private function xmlElementObjectToArray($simpleXmlElementObject, &$recursionDepth=0) {
        
        //Avoid deep recusions
        if ($recursionDepth > $this->_max_recursion_depth) {
            return false;
        }
    
        if ($recursionDepth == 0) {
            if (get_class($simpleXmlElementObject) != 'SimpleXMLElement') {
                return false;
            } else {
                $callerProvidedSimpleXmlElementObject = $simpleXmlElementObject;
            }
        } 
        
        if (is_object($simpleXmlElementObject)) {
      
            $copyOfsimpleXmlElementObject = $simpleXmlElementObject;
            $simpleXmlElementObject = get_object_vars($simpleXmlElementObject);
        }
        $copyOfsimpleXmlElementObject = $simpleXmlElementObject;
        
        // Get the object variables in the SimpleXmlElement object for us to iterate.
        // It needs to be an array of object variables.
        if (is_array($simpleXmlElementObject)) {

            $resultArray = array();

            //There are no children, we'll return the object.
            if (count($simpleXmlElementObject) <= 0) {
                if(empty($copyOfsimpleXmlElementObject)) {
                    return '';    
                }
                
                return (trim(strval($copyOfsimpleXmlElementObject)));
            }
    
            //Children exist, let's iterate through them.
            foreach($simpleXmlElementObject as $key=>$value) {
    
                $recursionDepth++;
                $resultArray[$key] = $this->xmlElementObjectToArray($value, $recursionDepth);
                $recursionDepth--;
            }
    
            if ($recursionDepth == 0) {
                $tempArray = $resultArray;
                $resultArray = array();
                $resultArray[$callerProvidedSimpleXmlElementObject->getName()] = $tempArray;
            }
    
            return $resultArray;
            
        } else {
        
            return trim(strval($simpleXmlElementObject));
            
        }
    }

} 

