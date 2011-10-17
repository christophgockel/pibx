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
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'Tests/Scenarios/Reference/Basic/QualifiedLocalElementsTest.php';
require_once 'Tests/Scenarios/Reference/Basic/TargetNamespaceTest.php';
require_once 'Tests/Scenarios/Reference/Basic/IdentifierNameTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NonIdentifierNameTest.php';
require_once 'Tests/Scenarios/Reference/Basic/StringElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/StringAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/BooleanElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/BooleanAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/DoubleElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/DoubleAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/GlobalSimpleTypeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/StringEnumerationTypeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ComplexTypeSequenceTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementMinOccurs1Test.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementMaxOccurs1Test.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementMaxOccursUnboundedTest.php';
require_once 'Tests/Scenarios/Reference/Basic/AttributeOptionalTest.php';
require_once 'Tests/Scenarios/Reference/Basic/GlobalElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementMinOccurs0Test.php';
require_once 'Tests/Scenarios/Reference/Basic/NillableElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NullEnumerationTypeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementEmptyComplexTypeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementEmptySequenceTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NestedSequenceElementListTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementReferenceTest.php';
require_once 'Tests/Scenarios/Reference/Basic/AttributeReferenceTest.php';
require_once 'Tests/Scenarios/Reference/Basic/IncludeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/SequenceMinOccurs1Test.php';
require_once 'Tests/Scenarios/Reference/Basic/SequenceMaxOccurs1Test.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementMinOccurs0MaxOccursUnboundedTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementMinOccurs1MaxOccursUnboundedTest.php';
require_once 'Tests/Scenarios/Reference/Basic/AttributeTypeReferenceTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementTypeReferenceTest.php';
require_once 'Tests/Scenarios/Reference/Basic/LocalElementComplexTypeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/IdTest.php';
require_once 'Tests/Scenarios/Reference/Basic/GlobalElementComplexTypeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/GlobalAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/SequenceElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/SequenceSingleRepeatedElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/MinOccurs1Test.php';
require_once 'Tests/Scenarios/Reference/Basic/MaxOccurs1Test.php';
require_once 'Tests/Scenarios/Reference/Basic/ComplexTypeAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ComplexTypeAttributeExtensionTest.php';
require_once 'Tests/Scenarios/Reference/Basic/GlobalElementConcreteTest.php';
require_once 'Tests/Scenarios/Reference/Basic/GlobalComplexTypeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ComplexTypeConcreteTest.php';
require_once 'Tests/Scenarios/Reference/Basic/AttributeFormUnqualifiedTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NotMixedTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NotNillableElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ElementFormQualifiedTest.php';
require_once 'Tests/Scenarios/Reference/Basic/ComplexTypeSequenceExtensionTest.php';
require_once 'Tests/Scenarios/Reference/Basic/DateTimeElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/DateTimeAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/Base64BinaryElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/AnyURIElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/AnyURIAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/QNameElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/QNameAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NormalizedStringElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NormalizedStringAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/TokenElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/TokenAttributeTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NameElementTest.php';
require_once 'Tests/Scenarios/Reference/Basic/NameAttributeTest.php';
/**
 * Basic Reference Test-Suite.
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_Suite extends PHPUnit_Framework_TestSuite {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite();
        
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_QualifiedLocalElementsTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_TargetNamespaceTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_IdentifierNameTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NonIdentifierNameTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_StringElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_StringAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_BooleanElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_BooleanAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_DoubleElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_DoubleAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_GlobalSimpleTypeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_StringEnumerationTypeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ComplexTypeSequenceTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementMinOccurs1Test');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementMaxOccurs1Test');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementMaxOccursUnboundedTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_AttributeOptionalTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_GlobalElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementMinOccurs0Test');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NillableElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NullEnumerationTypeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementEmptyComplexTypeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementEmptySequenceTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NestedSequenceElementListTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementReferenceTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_AttributeReferenceTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_IncludeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_SequenceMinOccurs1Test');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_SequenceMaxOccurs1Test');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementMinOccurs0MaxOccursUnboundedTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementMinOccurs1MaxOccursUnboundedTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_AttributeTypeReferenceTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementTypeReferenceTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_LocalElementComplexTypeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_IdTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_GlobalElementComplexTypeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_GlobalAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_SequenceElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_SequenceSingleRepeatedElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_MinOccurs1Test');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_MaxOccurs1Test');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ComplexTypeAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ComplexTypeAttributeExtensionTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_GlobalElementConcreteTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_GlobalComplexTypeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ComplexTypeConcreteTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_AttributeFormUnqualifiedTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NotMixedTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NotNillableElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ElementFormQualifiedTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_ComplexTypeSequenceExtensionTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_DateTimeElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_DateTimeAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_Base64BinaryElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_AnyURIElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_AnyURIAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_QNameElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_QNameAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NormalizedStringElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NormalizedStringAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_TokenElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_TokenAttributeTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NameElementTest');
        $suite->addTestSuite('PiBX_Scenarios_Reference_Basic_NameAttributeTest');

        return $suite;
    }
}
