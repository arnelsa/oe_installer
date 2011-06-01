<?php
class PhraseBySpecialtyTest extends CDbTestCase
{
	public $fixtures = array(
		'sections' => 'Section',
		'sectionTypes' => 'SectionType',
		'services' => 'Service',
		'specialties' => 'Specialty',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'eventTypes' => 'EventType',
		'elementTypes' => 'ElementType',
		'possibleElementTypes' => 'PossibleElementType',
		'siteElementTypes' => 'SiteElementType',
		'phrasesBySpecialty'	=> 'PhraseBySpecialty',
		'phraseNames'	=> 'PhraseName'
	);


	public function testGet_InvalidParameters_ReturnsFalse()
	{
		$fakeId = 9999;
		$result = PhraseBySpecialty::model()->findByPk($fakeId);
		$this->assertNull($result);
	}

	public function testGet_ValidParameters_ReturnsCorrectData()
	{
		$fakeId = 9999;

		$expected = $this->phrasesBySpecialty('phraseBySpecialty1');
		$result = PhraseBySpecialty::model()->findByPk($expected['id']);

		$this->assertEquals(get_class($result), 'PhraseBySpecialty');
		$this->assertEquals($expected, $result);
	}

	public function testCreate()
	{
		$phrase = new PhraseBySpecialty;
		$phrase->setAttributes(array(
			'phrase' => 'Testing phrase',
			'section_id' => $this->sections['section1']['id'],
			'specialty_id' => $this->specialties['specialty1']['id'],
			'display_order' => 1,
			'phrase_name_id' => $this->phraseNames['phraseName1']['id'],
		));
		$this->assertTrue($phrase->save(true));
	}

	public function testUpdate()
	{
		$expected = 'Testing again';
		$phrase = PhraseBySpecialty::model()->findByPk($this->phrasesBySpecialty['phraseBySpecialty1']['id']);
		$phrase->phrase = $expected;
		$phrase->save();
		$phrase = PhraseBySpecialty::model()->findByPk($this->phrasesBySpecialty['phraseBySpecialty1']['id']);
		$this->assertEquals($expected, $phrase->phrase);
	}

	public function testDelete()
	{
		$phrase = PhraseBySpecialty::model()->findByPk($this->phrasesBySpecialty['phraseBySpecialty1']['id']);
		$phrase->delete();
		$result = PhraseBySpecialty::model()->findByPk($this->phrasesBySpecialty['phraseBySpecialty1']['id']);
		$this->assertNull($result);
	}

	public function testRelevantSectionTypesReturnsValidSectionTypeNames()
	{
		$relevantSectionTypes = PhraseBySpecialty::model()->relevantSectionTypes();
		$this->assertTrue(is_array($relevantSectionTypes));
		foreach ($relevantSectionTypes as $relevantSectionType) {
			$sectionType = SectionType::model()->findByAttributes(array('name' => $relevantSectionType));
			$this->assertInstanceOf('SectionType', $sectionType);
		}
	}
	public function testGetOverrideableNames()
	{
		$overrideable = PhraseBySpecialty::model()->getOverrideableNames($this->sections['section1']['id'], $this->firms['firm1']['id']);
		$this->assertTrue(is_array($overrideable));

		$expected = array('Congenital Cataract', 'unnatural cataract');
		$count = 0;
		foreach ($overrideable as $o) {
			$this->assertEquals($expected[$count], $o->name);
			$count++;
		}	
	}
}
