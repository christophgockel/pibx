<?php
/**
 * Copyright (c) 2010-2011, Christoph Gockel.
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
require_once 'PiBX/CodeGen/ASTConstructor.php';
/**
 * This pattern matcher gets feeded with subsequent ParseTree elements.
 * After every insertion, the matcher can be asked whether a matched AST does
 * exist.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ParseTreePatternMatcher {
    private $typePatterns;
    private $stackOfElements;

    private $currentStackElement;
    private $currentStackPosition;

    public function __construct() {
        $this->buildBasicPatterns();
        $this->reset();
    }

    public function reset() {
        $this->stackOfElements = array();
    }

    private function buildBasicPatterns() {
        $this->typePatterns = array(
            array(
                'PiBX_ParseTree_ComplexTypeNode'
            ),
            array(
                'PiBX_ParseTree_SimpleTypeNode'
            ),
            array(
                'PiBX_ParseTree_ElementNode',
                'PiBX_ParseTree_ComplexTypeNode',
                'PiBX_ParseTree_SequenceNode'
            ),
        );
    }

    public function addElement(PiBX_ParseTree_Tree $element) {
        $this->stackOfElements[] = $element;
    }

    public function elementsMatch() {
        $matches = $this->matchElementsInArray($this->typePatterns);

        return count($matches) > 0;
    }

    private function matchElementsInArray($array) {
        $matchedTypes = array();

        foreach ($this->stackOfElements as $position => &$element) {
            $this->currentStackElement = get_class($element);
            $this->currentStackPosition = $position;

            $matchedTypes = array_filter($array, array($this, 'currentElementMatchesForArray'));
        }

        return $matchedTypes;
    }

    private function currentElementMatchesForArray($subArray) {
        // this method is used for array_filter, so ignore the "unused" message from your IDE
        return $subArray[$this->currentStackPosition] == $this->currentStackElement;
    }

    public function elementsMatchDistinct() {
        $matches = $this->matchElementsInArray($this->typePatterns);

        return count($matches) == 1;
    }

    public function getMatchedAST() {
        if ( !$this->elementsMatchDistinct() ) {
            throw new RuntimeException('Invalid state for pattern matching');
        }

        if ($this->elementsMatchDistinct()) {
            return new PiBX_AST_Type();
        }
    }

    public function constructMatchedAST() {
        $constructor = new PiBX_CodeGen_ASTConstructor($this->stackOfElements);
        return $constructor->construct();
        //TODO: move out into separate classes!
            $tree = $this->stackOfElements[0];
            $name = ucfirst($tree->getName());
            $t = new PiBX_AST_Type($name);
//        //if ($this->countTypes() == 0) {
//            // the first type is the XSD-root.
            $t->setAsRoot();
            $t->setTargetNamespace($tree->getParent()->getTargetNamespace());
            $t->setNamespaces($tree->getNamespaces());
  //          $this->typeList[] = $t;
        return $t;
//        print_r($this->stackOfElements);
    }
}
