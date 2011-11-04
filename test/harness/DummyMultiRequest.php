<?php

/**
 * Licensed to Jasig under one or more contributor license
 * agreements. See the NOTICE file distributed with this work for
 * additional information regarding copyright ownership.
 * 
 * Jasig licenses this file to you under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at:
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once dirname(__FILE__).'/../../source/CAS/Request/MultiRequestInterface.php';

/**
 * This interface defines a class library for performing multiple web requests in batches.
 * Implementations of this interface may perform requests serially or in parallel.
 */
class CAS_TestHarness_DummyMultiRequest
	implements CAS_Request_MultiRequestInterface 
{
	private $requests = array();
	private $sent = false;

	/*********************************************************
	 * Add Requests
	 *********************************************************/

	/**
	 * Add a new Request to this batch.
	 * Note, implementations will likely restrict requests to their own concrete class hierarchy.
	 * 
	 * @param CAS_RequestInterface $request
	 * @return void
	 * @throws CAS_OutOfSequenceException If called after the Request has been sent.
	 * @throws CAS_InvalidArgumentException If passed a Request of the wrong implmentation.
	 */
	public function addRequest (CAS_RequestInterface $request) {
		if ($this->sent)
			throw new CAS_OutOfSequenceException('Request has already been sent cannot '.__METHOD__);
		if (!$request instanceof CAS_TestHarness_DummyRequest)
			throw new CAS_InvalidArgumentException('As a CAS_TestHarness_DummyMultiRequest, I can only work with CAS_TestHarness_DummyRequest objects.');
		
		$this->requests[] = $request;
	}

	/*********************************************************
	 * 2. Send the Request
	 *********************************************************/

	/**
	 * Perform the request. After sending, all requests will have their responses poulated.
	 *
	 * @return boolean TRUE on success, FALSE on failure.
	 * @throws CAS_OutOfSequenceException If called multiple times.
	 */
	public function send () {
		if ($this->sent)
			throw new CAS_OutOfSequenceException('Request has already been sent cannot send again.');
		if (!count($this->requests))
			throw new CAS_OutOfSequenceException('At least one request must be added via addRequest() before the multi-request can be sent.');
		
		$this->sent = true;
		
		// Run all of our requests.
		foreach ($this->requests as $request) {
			$request->send();
		}
	}
	
	/**
	 * Retrieve the number of requests added to this batch.
	 * 
	 * @return number of request elements
	 */
	public function getNumRequests() {
		if ($this->sent)
			throw new CAS_OutOfSequenceException('Request has already been sent cannot '.__METHOD__);
		return count($this->requests);
	}
}