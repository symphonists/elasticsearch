<?php
/**
 * Elastica result set
 *
 * List of all hits that are returned for a search on elasticsearch
 * Result set implents iterator
 *
 * @category Xodoa
 * @package Elastica
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class Elastica_ResultSet implements Iterator, Countable
{
	protected $_results = array();
	protected $_position = 0;
	protected $_response = null;

	/**
	 * Constructs ResultSet object
	 *
	 * @param Elastica_Response $response Response object
	 */
	public function __construct(Elastica_Response $response) {
		$this->rewind();
		$this->_init($response);
	}

	/**
	 * Loads all data into the results object (initalisation)
	 *
	 * @param Elastica_Response $response Response object
	 */
	protected function _init(Elastica_Response $response) {
		$this->_response = $response;
		$result = $response->getData();
		$this->_totalHits = $result['hits']['total'];
		$this->_maxScore = $result['hits']['max_score'];

		if (isset($result['hits']['hits'])) {
			foreach ($result['hits']['hits'] as $hit) {
				$this->_results[] = new Elastica_Result($hit);
			}
		}
	}

	/**
	 * Returns all results
	 *
	 * @return array Results
	 */
	public function getResults() {
		return $this->_results;
	}

	/**
	 * Returns whether facets exist
	 *
	 * @return boolean Facet existence
	 */
	public function hasFacets() {
		$data = $this->_response->getData();
		return isset($data['facets']);
	}

	/**
	 * Returns all facets results
	 *
	 * @return array Facet results
	 */
	public function getFacets() {
		$data = $this->_response->getData();
		return isset($data['facets']) ? $data['facets'] : array();
	}

	/**
	 * Returns the total number of found hits
	 *
	 * @return int Total hits
	 */
	public function getTotalHits() {
		return (int) $this->_totalHits;
	}
	
	/**
	 * Returns the highest score from the found hits
	 *
	 * @return float Max score
	 */
	public function getMaxScore() {
		return (float) $this->_maxScore;
	}

	/**
	 * Returns response object
	 *
	 * @return Elastica_Response Response object
	 */
	public function getResponse() {
		return $this->_response;
	}

	/**
	 * Returns size of current set
	 *
	 * @return int Size of set
	 */
	public function count() {
		return sizeof($this->_results);
	}


	/**
	 * Returns the current object of the set
	 *
	 * @return mixed|bool Set object or false if not valid (no more entries)
	 */
	public function current() {
		if ($this->valid()) {
			return $this->_results[$this->key()];
		} else {
			return false;
		}
	}

	/**
	 * Sets pointer (current) to the next item of the set
	 */
	public function next() {
		$this->_position++;
		return $this->current();
	}

	/**
	 * Returns the position of the current entry
	 *
	 * @return int Current position
	 */
	public function key() {
		return $this->_position;
	}

	/**
	 * Check if an object exists at the current position
	 *
	 * @return bool True if object exists
	 */
	public function valid() {
		return isset($this->_results[$this->key()]);
	}

	/**
	 * Resets position to 0, restarts iterator
	 */
	public function rewind() {
		$this->_position = 0;
	}
}