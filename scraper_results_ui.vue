                  <!-- Results -->
                  <div v-if="scrapeResults" class="mt-4">
                    <div v-if="scrapeResults.success" class="card border-success">
                      <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                          <i class="la la-check-circle"></i> 
                          Scraping Successful - {{ scrapeResults.company }}
                        </h5>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="text-center p-3 border rounded bg-light">
                              <h3 class="text-primary mb-1">{{ scrapeResults.total_found }}</h3>
                              <small class="text-muted">Total Contacts Found</small>
                            </div>
                          </div>
                          <div class="col-md-4" v-if="scrapeResults.emails.length">
                            <div class="text-center p-3 border rounded bg-light">
                              <h3 class="text-success mb-1">{{ scrapeResults.emails.length }}</h3>
                              <small class="text-muted">Email Addresses</small>
                            </div>
                          </div>
                          <div class="col-md-4" v-if="scrapeResults.phones.length">
                            <div class="text-center p-3 border rounded bg-light">
                              <h3 class="text-info mb-1">{{ scrapeResults.phones.length }}</h3>
                              <small class="text-muted">Phone Numbers</small>
                            </div>
                          </div>
                        </div>

                        <div class="row mt-4" v-if="scrapeResults.emails.length || scrapeResults.phones.length">
                          <div class="col-md-6" v-if="scrapeResults.emails.length">
                            <h6 class="text-success"><i class="la la-envelope"></i> Email Addresses</h6>
                            <div class="bg-light p-3 rounded">
                              <div v-for="email in scrapeResults.emails" :key="email" class="mb-2">
                                <span class="badge badge-success mr-2">✓</span>
                                <code>{{ email }}</code>
                              </div>
                            </div>
                          </div>
                          
                          <div class="col-md-6" v-if="scrapeResults.phones.length">
                            <h6 class="text-info"><i class="la la-phone"></i> Phone Numbers</h6>
                            <div class="bg-light p-3 rounded">
                              <div v-for="phone in scrapeResults.phones" :key="phone" class="mb-2">
                                <span class="badge badge-info mr-2">✓</span>
                                <code>{{ phone }}</code>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div v-if="scrapeResults.leads_created" class="alert alert-success mt-4 mb-0">
                          <div class="d-flex align-items-center">
                            <i class="la la-database text-success mr-3" style="font-size: 2rem;"></i>
                            <div>
                              <h6 class="mb-1">Leads Successfully Created!</h6>
                              <p class="mb-0">
                                <strong>{{ scrapeResults.emails.length }}</strong> email(s) and 
                                <strong>{{ scrapeResults.phones.length }}</strong> phone(s) saved to database.
                                <br><small class="text-muted">Check the leads table below to view your new contacts.</small>
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div v-else class="alert alert-danger">
                      <div class="d-flex align-items-center">
                        <i class="la la-exclamation-triangle text-danger mr-3" style="font-size: 2rem;"></i>
                        <div>
                          <h6 class="mb-1">Scraping Failed</h6>
                          <p class="mb-0">{{ scrapeResults.message }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Bulk Results -->
                  <div v-if="bulkResults" class="mt-4">
                    <div class="card border-primary">
                      <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                          <i class="la la-globe"></i> 
                          Bulk Scraping Results - {{ bulkResults.total_websites }} Websites
                        </h5>
                      </div>
                      <div class="card-body">
                        <div class="row mb-3">
                          <div class="col-md-12">
                            <div class="progress mb-2">
                              <div class="progress-bar bg-success" style="width: 100%">
                                Completed: {{ bulkResults.total_websites }} websites
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="accordion" id="bulkResultsAccordion">
                          <div v-for="(result, index) in bulkResults.results" :key="index" class="card mb-2">
                            <div class="card-header p-2" :id="`heading${index}`">
                              <button 
                                class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center"
                                type="button" 
                                data-toggle="collapse" 
                                :data-target="`#collapse${index}`"
                              >
                                <div>
                                  <i class="la la-globe mr-2"></i>
                                  {{ result.website }}
                                </div>
                                <span :class="['badge', result.success ? 'badge-success' : 'badge-danger']">
                                  {{ result.success ? `${result.total_found} contacts` : 'Failed' }}
                                </span>
                              </button>
                            </div>
                            <div 
                              :id="`collapse${index}`" 
                              class="collapse" 
                              :data-parent="`#bulkResultsAccordion`"
                            >
                              <div class="card-body" v-if="result.success">
                                <div class="row">
                                  <div class="col-md-4">
                                    <strong>Company:</strong><br>
                                    <span class="badge badge-secondary">{{ result.company }}</span>
                                  </div>
                                  <div class="col-md-4">
                                    <strong>Emails:</strong><br>
                                    <div v-if="result.emails.length">
                                      <code v-for="email in result.emails" :key="email" class="d-block">{{ email }}</code>
                                    </div>
                                    <span v-else class="text-muted">None found</span>
                                  </div>
                                  <div class="col-md-4">
                                    <strong>Phones:</strong><br>
                                    <div v-if="result.phones.length">
                                      <code v-for="phone in result.phones" :key="phone" class="d-block">{{ phone }}</code>
                                    </div>
                                    <span v-else class="text-muted">None found</span>
                                  </div>
                                </div>
                              </div>
                              <div class="card-body text-danger" v-else>
                                <i class="la la-exclamation-triangle"></i> {{ result.message || 'Scraping failed for this website' }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>