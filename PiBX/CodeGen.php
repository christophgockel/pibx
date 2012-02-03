<?php
/**
 * Copyright (c) 2010-2012, Christoph Gockel <christoph@pibx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * * Neither the name of PiBX nor the names of its contributors may be used
 *   to endorse or promote products derived from this software without specific
 *   prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/ASTOptimizer.php';
require_once 'PiBX/CodeGen/ClassGenerator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
require_once 'PiBX/CodeGen/TypeUsage.php';
require_once 'PiBX/Binding/Creator.php';
/**
 * PiBX_CodeGen is used to generate PHP classes from a given XSD-Schema file.
 *
 * It is constructed like a classical compiler. It goes through different
 * phases like: parsing the input stream, building an abstract syntax tree
 * and so on.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen {
    /**
     * Creating an object of PiBX_CodeGen starts the code generation.
     *
     * A valid schema-file has to be passed.
     * Options can be passed as an associative array with a value of a boolean "true".
     * This way the code generation can be customized.
     * 
     * Possible option-values are (keys of "$options"):
     *     - "typechecks" enables the generation of type-check code into setter methods.
     * 
     * @param string $schemaFile
     * @param array $options
     */
    public function  __construct($schemaFile, $options) {
        $typeUsage = new PiBX_CodeGen_TypeUsage();
        
        // phase 1
        echo "Parsing schema file '$schemaFile'\n";
        $parser = new PiBX_CodeGen_SchemaParser($schemaFile, $typeUsage);
        $parsedTree = $parser->parse();
        
        // phase 2
        echo "Creating abstract syntax tree\n";
        $creator = new PiBX_CodeGen_ASTCreator($typeUsage);
        $parsedTree->accept($creator);

        $typeList = $creator->getTypeList();

        // phase 3
        print "Optimizing abstract syntax tree\n";
        print "    Before: " . count($typeList) . " type(s)\n";
        $usages = $typeUsage->getTypeUsages();

        $optimizer = new PiBX_CodeGen_ASTOptimizer($typeList, $typeUsage);
        $typeList = $optimizer->optimize();
        print "    After:  " . count($typeList) . " type(s)\n";

        // phase 4
        print "Creating binding.xml\n";
        $b = new PiBX_Binding_Creator();
        
        foreach ($typeList as &$type) {
            $type->accept($b);
        }
        
        file_put_contents('./output/binding.xml', $b->getXml());
        
        // phase 5
        print "Generating classes to: ./output\n";
        $generator = new PiBX_CodeGen_ClassGenerator();

        if (array_key_exists('typechecks', $options) && $options['typechecks'] === true) {
            $generator->enableTypeChecks();
        }
        
        foreach ($typeList as &$type) {
            $type->accept($generator);
        }

        if (!is_dir('output')) {
            mkdir('output');
        }
        
        foreach ($generator->getClasses() as $className => $classCode) {
            $code = "<?php\n" . $classCode;
            
            file_put_contents('output/' . $className . '.php', $code);
        }
    }
}
