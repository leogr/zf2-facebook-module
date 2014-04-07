<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'FacebookModule\Facebook' => 'FacebookModule\Service\FacebookServiceFactory'
        ),
        'aliases' => array(
            'Facebook' => 'FacebookModule\Facebook'
        )
    )
);
