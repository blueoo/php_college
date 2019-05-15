<?php
class XStatic
{	
	private static $aliases=array();
	private static $classMap=array();
	
    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = array($alias => $path);
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = array(
                        $alias => $path,
                        $root => static::$aliases[$root],
                    );
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }
	
    public static function getAlias($alias, $throwException = true)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            } else {
                foreach (static::$aliases[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return $path . substr($alias, strlen($name));
                    }
                }
            }
        }

        if ($throwException) {
            throw new InvalidParamException("Invalid path alias: $alias");
        } else {
            return false;
        }
    }
	
	public static function autoload($className)
    {
		if(class_exists($className)){
			return;
		}
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
					
            $classFile = static::getAlias('@' . str_replace('\\', '/', str_replace('_','.',$className)) . '.class.php', false);
            if ($classFile === false || !is_file($classFile)) {
                $classFile = static::getAlias('@' . str_replace('\\', '/', str_replace('_','.',$className)) . '.php', false);{
                if ($classFile === false || !is_file($classFile)) {
                         return;
                     }
                }
                //return;
            }
        } else {
            return;
        }
        //echo $className ." : ".$classFile."\r\n";
        include($classFile);
		
        if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
           # throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
		
    }
}