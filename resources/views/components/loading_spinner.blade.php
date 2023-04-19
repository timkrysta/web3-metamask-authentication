<?php
// $script_allowed_to_visit have to be FALSE if we dont want to allow user to hand-type and run script alone
#$script_allowed_to_visit = FALSE;
#include_once $_SERVER['DOCUMENT_ROOT']."/02/set_up.php";
?>
    <style>
        #spinner,
        #spinner_on_white {
            display: none; /* Hidden by default */
        }

        #overlay,
        #overlay_white
        {
            position: fixed; /* Sit on top of the page content */
            width: 100%; /* Full width (cover the whole page) */
            height: 100%; /* Full height (cover the whole page) */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
            /*cursor: pointer;  Add a pointer on hover */

            display: flex;
            justify-content: center;
            align-items: center;
        }

        #overlay {
            background-color: rgba(0,0,0,0.6); /* Black background with opacity 0.6 */
        }
        #overlay_white {
            background-color: rgba(255,255,255,1); /* White background with opacity 1 */
            /** below 2 lines for display Loading text below spinner */
            display: flex;
            flex-direction: column;
        }

    </style>

	<div id="spinner">
    	<div id="overlay">
            <div class="spinner-border text-light" role="status" style="display: inherit;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>


    <div id="spinner_on_white">
    	<div id="overlay_white">
            <div class="spinner-border text-primary" role="status" style="display: inherit; width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>

            </div>
            <div style="margin-top:1rem;">
                Please wait...
            </div>
        </div>
    </div>

