<?php
/*
 * This file is part of the sfI18NGettextPlural package.
 * (c) Edwood Ng
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this plugin.
 */

/**
 * sfMessageFormatPlural class provides formatting functions for proper plural translation
 *
 *
 * @package    sfI18NGettextPlural
 * @author     Edwood Ng <ed@edng.org>
 * @version    SVN: $Id$
 */
class sfMessageFormatPlural extends sfMessageFormat
{
  /**
   * Call formatStringPlural for plural translation formatting.  Equivalent to sfMessageFormat->format function.  
   *
   * @see sfMessageFormat
   */
  public function formatPlural($string, $plural, $args = array(), $catalogue = null, $charset = null)
  {
    // make sure that objects with __toString() are converted to strings
    $string = (string) $string;
    if (empty($charset))
    {
      $charset = $this->getCharset();
    }

    $s = $this->formatStringPlural(sfToolkit::I18N_toUTF8($string, $charset), $plural, $args, $catalogue);

    return sfToolkit::I18N_toEncoding($s, $charset);
  }

  /**
   * Calculate plural forms index based on plural formula and will return proper plural form translation.  
   * Equivalent to sfMessageFormat->formatString function.  
   *
   * @see sfMessageFormat
   */
  protected function formatStringPlural($string, $plural, $args = array(), $catalogue = null)
  {
    if (empty($args))
    {
      $args = array();
    }

    if (empty($catalogue))
    {
      $catalogue = empty($this->catalogue) ? 'messages' : $this->catalogue;
    }

    $this->loadCatalogue($catalogue);

    foreach ($this->messages[$catalogue] as $variant)
    {
      // we found it, so return the target translation
      if (isset($variant[$string]))
      {
        $target = $variant[$string]; 

        if (is_array($target)) {
          $target = array_shift($target);
        }

        if ($plural) {
          $n = reset($args);
          // search for plural form variant
          $pluralTarget = $variant["$string|$plural"]; 

          if (is_array($pluralTarget)) {
            $pluralTarget = array_shift($pluralTarget);
          }

          if ($pluralTarget) {
            // calculate plural form index
            $formula = array_shift($variant['%PLURAL-FORMS%']);
            $pid = @eval('return '.str_replace('n',$n,$formula));
            if (isset($pluralTarget[$pid])) {
              $target = $pluralTarget[$pid];
            } else {
              // target not found, it's likely due to missing data, return the first translation
              $target = array_shift($pluralTarget);
            }
          } else {
            // plural form does not exist, this is probably the default languge, use a simple plural form instead
            if ($n > 1)
              $target = $plural;
            else
              $target = $string;
          }
        }

        // found, but untranslated
        if (empty($target))
        {
          return $this->postscript[0].$this->replaceArgs($string, $args).$this->postscript[1];
        }
        return $this->replaceArgs($target, $args);
      }
    }
    
    // check if plural form is needed for the default language, use simple plural form rule
    if (empty($target) && $plural && !empty($args)) {
      $n = reset($args);
      if ($n > 1) 
        $string = $plural;
    }

    // well we did not find the translation string.
    $this->source->append($string);

    return $this->postscript[0].$this->replaceArgs($string, $args).$this->postscript[1];
  }
}
