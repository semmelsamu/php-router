<?php
# ROUTER.PHP - Routing functions

class Php_router
{
    private $routes;

    function __construct($routes)
    {
        $this->routes = $routes;
    }

    private function route_inner($request, $routes)
    {
        $return_value = [
            ROUTES_PATH => null,
            ROUTES_ARGS => null,
        ];

        # Process the next element of the request queue:
        $current_path = array_shift($request);
        $current_urls = array_column($routes, ROUTES_URL);

        # Check if the current request contains something, else we need to return the index file:
        if(isset($current_path))
        {
            # Check if a mathing url in the routes list exists
            if(in_array($current_path, $current_urls))
            {
                $current_url = $routes[array_search($current_path, $current_urls)];
                # Check if the request contains further elements
                if(!empty($request))
                {
                    # Check if we have suburls we can process further
                    if(isset($current_url[ROUTES_SUBURLS]))
                    {
                        $return_value[ROUTES_PATH] = $this->route_inner($request, $current_url[ROUTES_SUBURLS])[ROUTES_PATH];
                    }
                }
                else
                {
                    $return_value[ROUTES_PATH] = $current_url[ROUTES_PATH];
                }
            }
        }
        else
        {
            # Return the index page (if existing):
            if(in_array(ROUTES_INDEX_URL, $current_urls))
            {
                $return_value[ROUTES_PATH] = $routes[array_search(ROUTES_INDEX_URL, $current_urls)][ROUTES_PATH];
            }
        }

        return $return_value;
    }

    public function get_route($initial_request) {
        # We use arrays to explode the long request string into smaller bits, which are better to process:
        $initial_request = explode("/", trim($initial_request, " /"));

        # To get the actual request our router should handle, we need to cut off the base path directories:
        $base_directories = explode("/", trim(BASE_PATH, " /"));
        $request = array_slice($initial_request, sizeof($base_directories));

        return $this->route_inner($request, $this->routes);
    }

    public function route($initial_request)
    {
        # This function just auto-includes the routed file. If we din't find any file, we just include the 404 document.
        $route = $this->get_route($initial_request);

        if(isset($route[ROUTES_PATH]))
        {
            # File found, include it and, if existing, return arguments
            include(getcwd().HTDOCS_PATH."/".$route[ROUTES_PATH]);
            return isset($route[ROUTES_ARGS]) ? $route[ROUTES_ARGS] : null;
        }
        else
        {
            # No file found, include 404 document and return no arguments
            http_response_code(404);
            include(getcwd().HTDOCS_PATH."/".PATH_TO_404);
            return null;
        }
    }
}



?>