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
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'Tests/Scenarios/Reference/TestCase.php';
/**
 * Implementation of the W3C advanced example "NoTargetNamespace".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Advanced_NoTargetNamespaceTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Advanced/NoTargetNamespace';
        $this->schemaFile = 'NoTargetNamespace.xsd';
    }

    public function getASTs() {
        $namespaces = array(
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );

        $type1 = new PiBX_AST_Type('noTargetNamespace', 'string');
        $type1->setAsRoot();
        $type1->setNamespaces($namespaces);
        
        return array($type1);
    }

    public function getParseTree() {
        $tree = new PiBX_ParseTree_RootNode();
        
        $namespaces = array(
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );

        $node1_element = new PiBX_ParseTree_ElementNode(array('name' => 'noTargetNamespace', 'type' => 'string'), 0);
        $node1_element->setNamespaces($namespaces);

        $tree->add($node1_element);
        
        return $tree;
    }
}
