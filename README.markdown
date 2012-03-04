PiBX
====
XML-Data-Binding for PHP
------------------------

With PiBX you can generate PHP classes based on an available XML-Schema. These classes can be used to marshal the informations to XML without hassling with schema checks, constraints or restrictions.

Originally, this project started as a port of the Java-framework JiBX. Since a plain port to PHP isn’t possible, PiBX concentrates to support identical features of JiBX. Especially regarding the flexible binding options.

Requirements
------------

* PHP 5.1+

Optional:

* PHPUnit 3.5+

Installation
------------

After downloading PiBX, the base directory should be available in PHP's include path.

It's up to you and your project's requirements and dependencies, whether you include the "Tests" directory and CodeGen.php (the command line program) or not. These parts of PiBX are not required at runtime.

Notes
-----
You can adapt your existing classes as well. The generating part of PiBX is optional. You then have to create a binding.xml file manually.

A binding.xml is needed at runtime. It defines all the mapping between PHP and XML.


License
-------
PiBX started as a port of JiBX, so it's licensed under the simplified BSD license as well.
You can find and view the license in the LICENSE file located in the PiBX root or under http://www.opensource.org/licenses/BSD-3-Clause.