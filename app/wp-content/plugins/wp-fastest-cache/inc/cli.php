<?php
/**
 * WP-CLI commands for WP Fastest Cache.
 */

if(!defined('ABSPATH')){
    exit;
}

// This is a WP-CLI command, so bail if it's not available.
if(!defined('WP_CLI')){
    return;
}


class wpfcCLI extends \WP_CLI_Command
{
    /**
     * Clears the cache.
     *
     * ## EXAMPLES
     *      wp fastest-cache clear all
     *      wp fastest-cache clear all and minified
     *
     *
     * @subcommand clear
     *
     * @param array $args Args.
     * @param array $args_assoc Associative args.
     *
     * @return void
     */
    public function wrong_usage(){
        $message = array("*************************************************************************",
                         "* Wrong usage!                                                          *",
                         "* Please read: https://www.wpfastestcache.com/features/wp-cli-commands/ *",
                         "*************************************************************************");
        WP_CLI::error_multi_line($message);
    }

    public function clear($args, $args_assoc){
        if(isset($GLOBALS['wp_fastest_cache'])){
            if(method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')){
                if(isset($args_assoc["post_id"])){
                    $post_ids = explode(',' , $args_assoc['post_id'] );

                    foreach($post_ids as $post_id){

                        WP_CLI::line("Clearing the cache of the post with ID number ".$post_id);
                        $GLOBALS['wp_fastest_cache']->singleDeleteCache(false, $post_id);
                        WP_CLI::success("The cache has been cleared!");
                    }

                }else if(isset($args[0])){
                    if($args[0] == "all"){
                        if(isset($args[1]) && isset($args[2])){
                            if($args[1] == "and" && $args[2] == "minified"){
                                WP_CLI::line("Clearing the ALL cache...");
                                $GLOBALS['wp_fastest_cache']->deleteCache(true);
                                WP_CLI::success("The cache has been cleared!");
                            }else{
                                self::wrong_usage();
                            }
                        }else{
                            WP_CLI::line("Clearing the ALL cache...");
                            $GLOBALS['wp_fastest_cache']->deleteCache();
                            WP_CLI::success("The cache has been cleared!");
                        }
                    }else{
                        self::wrong_usage();
                    }
                }else{
                    self::wrong_usage();
                }
            }else{
                WP_CLI::error("deleteCache() does not exist!");
            }
        }else{
            WP_CLI::error("GLOBALS['wp_fastest_cache'] has not been defined!");
        }
    }
}

WP_CLI::add_command( 'fastest-cache', 'wpfcCLI' );

?>