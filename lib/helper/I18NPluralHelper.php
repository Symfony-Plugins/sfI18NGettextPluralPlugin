<?
/*
 * This file is part of the sfI18NGettextPlural package.
 * (c) Edwood Ng
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this plugin.
 */

/**
 * This file contains a new helper function for displaying transltion in plural form.
 *
 * @package    sfI18NGettextPlural
 * @author     Edwood Ng <ed@edng.org>
 * @version    SVN: $Id$
 */

/**
 * Call the appropriate __plural function in sfI18NGettextPlural class for plural form 
 * translation.
 *
 * @see I18NPluralHelper
 */
function __plural($text, $plural, $args = array(), $catalogue = 'messages')
{
  if (sfConfig::get('sf_i18n'))
  {
    return sfContext::getInstance()->getI18N()->__plural($text, $plural, $args, $catalogue);
  }
  else
  {
    if (empty($args))
    {
      $args = array();
    }

    // replace object with strings
    foreach ($args as $key => $value)
    {
      if (is_object($value) && method_exists($value, '__toString'))
      {
        $args[$key] = $value->__toString();
      }
    }

    return strtr($text, $args);
  }
}
