<?php
/**
 * Copyright (c) 2010-2011, Christoph Gockel <christoph@pibx.de>.
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
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
/**
 * Implementation of the W3C basic example "ImportNamespace".
 * 
 * The implementation differs from the other testcases in the basic suite.
 * The schema, used in this testcase, uses an xsd:import element.
 * It specifies the attribute "namespace" only, but no "schemaLocation".
 * Therefore it has no practical relevance at the moment, and thus throws an
 * exception when trying to use it in a schema.
 * This conforms to the JiBX implementation where it yields the following
 * error message:
 *   >  ERROR validation.ValidationContext - Error: No known schema for
 *   >  namespace http://example.com/a/namespace for import at (line 21,
 *   >  col 59, in SequenceElement.xsd)
 * 
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_ImportNamespaceTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException RuntimeException
     */
    public function testSchemaShouldThrowException() {
        $pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/ImportNamespace';
        $schemaFile = 'ImportNamespace.xsd';

        $schemaFile = $pathToTestFiles . DIRECTORY_SEPARATOR . $schemaFile;

        $parser = new PiBX_CodeGen_SchemaParser($schemaFile);
        $parser->parse();
    }
}