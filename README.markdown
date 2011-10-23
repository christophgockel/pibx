PiBX
====

PiBX is an XML-Data-Binding framework for PHP.

With PiBX you can generate PHP classes based on an available XML-Schema. These classes can be used to marshal the informations to XML without hassling with schema checks, constraints or restrictions.

Originally, this project started as a port of the Java-frameworks JiBX. Since a plain port to PHP isn’t possible, PiBX concentrates to support identical features of JiBX. Especially regarding the flexible binding options.

Requirements
------------

* PHP 5.1+

Installation
------------

After you downloaded PiBX, the base directory should be available in PHP's include path.

It's up to you and your project's requirements and dependencies, whether you include the "Tests" directory and CodeGen.php (the command line program) or not. These parts of PiBX are not required at runtime.

License
-------
Since PiBX started as a port of JiBX, it's licensed under the simplified BSD license as well.
You can find and view the license in the LICENSE file located in the PiBX root.