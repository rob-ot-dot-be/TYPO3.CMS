<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Stefan Galinski <stefan.galinski@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Testcase for class t3lib_tree_pagetree_DataProvider.
 *
 * @author Stefan Galinski <stefan.galinski@gmail.com>
 * @package TYPO3
 * @subpackage t3lib
 */
class t3lib_tree_pagetree_DataProviderTest extends tx_phpunit_testcase {
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|t3lib_tree_pagetree_DataProvider
	 */
	protected $fixture = NULL;

	/**
	 * @var array
	 */
	protected $backupGlobalVariables = array();

	public function setUp() {
		$this->backupGlobalVariables['TYPO3_CONF_VARS'] = $GLOBALS['TYPO3_CONF_VARS'];
		$GLOBALS['TYPO3_CONF_VARS']['BE']['pageTree']['preloadLimit'] = 0;
	}

	public function tearDown() {
		unset($this->fixture);

		foreach ($this->backupGlobalVariables as $name => $value) {
			$GLOBALS[$name] = $value;
		}
	}

	/**
	 * @test
	 */
	public function getNodesSetsIsMountPointField() {
		$subpages = array(
			array(
				'uid' => 1,
				'isMountPoint' => FALSE,
			),
			array(
				'uid' => 2,
				'isMountPoint' => TRUE,
			),
			array(
				'uid' => 3,
			),
		);

		$subpagesWithWorkspaceOverlay = array(
			array(
				'uid' => 1,
				'title' => 'Home',
			),
			array(
				'uid' => 2,
				'title' => 'service',
			),
			array(
				'uid' => 3,
				'title' => 'contact',
			),
		);

		$this->fixture = $this->getMock('t3lib_tree_pagetree_DataProvider', array('getSubpages', 'getRecordWithWorkspaceOverlay'));
		$this->fixture->expects($this->once())->method('getSubpages')
			->will($this->returnValue($subpages));

		$this->fixture->expects($this->at(1))->method('getRecordWithWorkspaceOverlay')->with(1)
			->will($this->returnValue($subpagesWithWorkspaceOverlay[0]));
		$this->fixture->expects($this->at(2))->method('getRecordWithWorkspaceOverlay')->with(2)
			->will($this->returnValue($subpagesWithWorkspaceOverlay[1]));
		$this->fixture->expects($this->at(3))->method('getRecordWithWorkspaceOverlay')->with(3)
			->will($this->returnValue($subpagesWithWorkspaceOverlay[2]));

		$node = new t3lib_tree_Node();
		$node->setId(12);

		$nodeCollection = $this->fixture->getNodes($node);

		/** @var $node t3lib_tree_pagetree_Node */
		$isMountPointResult = array();
		foreach ($nodeCollection as $node) {
			$isMountPointResult[] = $node->isMountPoint();
		}
		$this->assertSame(array(FALSE, TRUE, FALSE), $isMountPointResult);
	}
}

?>