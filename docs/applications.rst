Applicaions
===========

(Section yet to be written)

The application schema
**********************

(Section yet to be written)

The client side
***************

(Section yet to be written)

The server side
***************

(Section yet to be written)

Packaging applications
**********************

Application can be packed into bundles using special composer package type *comodojo-app*.

.. code-block:: js

    {
        "name": "my/app",
        "description": "My first comodojo app",
        "type": "comodojo-app",
        "extra": {
            "comodojo-app-register": {
                "helloworld": {
                    "description": "My first comodojo app",
                    "assets": "assets"
                }
            }
            "comodojo-configuration-register": {
                "HELLOWORLD_DEFAULT_USER": "comodojo"
            }
            "comodojo-rpc-register": {

            }
            "comodojo-service-route": {

            }
            "comodojo-task-register": [
                {
	        		"name": "HelloWorld",
	        		"class": "\\My\\Tasks\\HelloWorldTask",
	        		"description": "Greetings from comodojo"
	        	}
            ],
            "comodojo-command-register": {
                "helloworld": {
                    "class": "My\\App\\Command\\Helloworld",
                    "description": "Greetings from comodojo",
                    "aliases": ["hw"],
                    "options": {},
                    "arguments": {
                        "to": {
                            "choices": {},
                            "multiple": false,
                            "optional": true,
                            "description": "hello to..."
                        }
                    }
                }
            }
        },
        "autoload": {
            "psr-4": {
                 "My\\App\\": "src"
             }
        }
    }
