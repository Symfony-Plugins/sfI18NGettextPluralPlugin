<?php
/*
 * This file is part of the sfI18NGettextPlural package.
 * (c) Edwood Ng
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this plugin.
 */

/**
 * sfMessageSource_gettextPlural class extends from sfMessageSource_gettext to generate
 * additional key value pairs on plural forms.  It also extract Plural-Forms formula
 * from the meta data section for plural form calculation.
 *
 *
 * @package    sfI18NGettextPlural
 * @author     Edwood Ng <ed@edng.org>
 * @version    SVN: $Id$
 */
class sfMessageSource_gettextPlural extends sfMessageSource_gettext
{
  /**
   * Loads the messages from a MO file.
   *
   * @param string $filename MO file.
   * @return array of messages.
   * @see sfMessageSource_gettext
   */
  public function &loadData($filename)
  {
    $mo = TGettext::factory('MO',$filename);
    $mo->load();
    $result = $mo->toArray();

    $this->pluralizeResult($result);

    $results = array();
    $count = 0;

    // add plural forms support
    foreach ($result['meta'] as $source => $target)
    {
      if ($source == 'Plural-Forms') 
      {
          $source = '%PLURAL-FORMS%';
          // trim $target
          $target = trim(substr($target, strpos($target, 'plural=') + 7));
          $results[$source][] = $target;  //target
          $results[$source][] = $count++; //id
          $results[$source][] = '';       //comments
          break;
      }
    }
    // make sure first entry is always used for plural forms
    if ($count == 0)
    {
      $source = '%PLURAL-FORMS%';
      $results[$source][] = '';  //target
      $results[$source][] = $count++; //id
      $results[$source][] = '';       //comments
    }

    foreach ($result['strings'] as $source => $target)
    {
      $results[$source][] = $target;  //target
      $results[$source][] = $count++; //id
      $results[$source][] = '';       //comments
    }

    return $results;
  }

  /**
   * Generate plural form key value pairs
   */
  public function pluralizeResult(&$result)
  {
    foreach ($result['strings'] as $source => $target) 
    {
      $sourceTokens = explode("\0", $source);
      if (count($sourceTokens) > 1) {
        // delete the merged entry
        unset($result['strings'][$source]);
        // tokenize plural forms
        $targetTokens = explode("\0", $target);
        $result['strings'][$sourceTokens[0]] = $targetTokens[0];
        for ($i = 0; $i < count($targetTokens); $i++) {
          $result['strings'][$sourceTokens[0].'|'.$sourceTokens[1]][$i] = $targetTokens[$i];
        }
      }
    }
  }
}
