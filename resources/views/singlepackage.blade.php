@extends('layouts.dashboard_frontend')
@section('content')

<div class="single_package"> 
	<div class="inner_single_package">
		<div class="container-fluid">
			<div class="row"> 
				<div class="col-md-3">
				</div> 
				<div class="col-md-9">
					<div class="row">
						<div class="col-lg-8">
							<div class="main_title_3">
								<span><em></em></span>
								<h2>Best Kerala Holiday Packages</h2>
							</div>							
						</div>                
						<div class="col-lg-4" style="padding-top:20px; text-align:right;">
							<div class="row">
							</div>
						</div> 
					</div>  
					<div id="ajaxResultContainer">
						<div class="row">
							<div class="col-lg-12">
								<div class="tourpack-pagtbox">
									<div class="row">
										<div class="col-lg-4 col-md-4 col-sm-12 pagtextbx pagt-tetbx">Showing : 1-20 out of 62</div>                        
										<div class="col-lg-5 col-md-5 col-sm-12 pagtwrap">
											<ul class="pagination">
												<li class="disabled"><a href="#">Prev</a></li>
												<li class="active"><a href="#">1</a></li>
												<li><a href="#" onclick="showPage(1); return false;">2</a></li>
												<li><a href="#" onclick="showPage(2); return false;">3</a></li>
												<li><a href="#" onclick="showPage(3); return false;">4</a></li>
												<li><a href="#" onclick="showPage(1); return false;">Next</a></li>
											</ul>                           
										</div>                        
										<div class="col-lg-3 col-md-3 col-sm-12 pagtboxsel sorting-box">
											<div class="custom-select selct-bg">
												<select class="form-control" name="sortBy" id="SortBy">
													<option value="order_id asc" selected="selected">Sort by</option>
													<option value="days asc">Duration Short</option>
													<option value="days desc">Duration Long</option>
													<option value="price_inr asc">Price Lowest First</option>
													<option value="price_inr desc">Price Highest First</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>     												
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/kerala-houseboat-tour-alleppey.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/kerala-houseboat-tour-alleppey-951.jpeg" width="250" class="img-fluid lazy" alt="5 Day Kerala Houseboat Tour Alleppey" title="" src="https://ihpl.b-cdn.net/pictures/tourintro/kerala-houseboat-tour-alleppey-951.jpeg" style="display: block;">
										</a>
									</div>                                
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>4 Nights / 5 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-houseboat-tour-alleppey.html">5 Day Kerala Houseboat Tour Alleppey</a>
												<p>Cochin- Alleppey- Cochin</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Excursion to attractions in Cochin</li>
													<li>Capture the sunset views</li>
													<li>Relax yourself on a houseboat ride</li>
													<li>Rejuvenate amid the backwaters</li>
												</ul>
											</div>							
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-houseboat-tour-alleppey.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('5 Day Kerala Houseboat Tour Alleppey'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/best-of-kerala-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/best-of-kerala-tour-16.jpeg" width="250" class="img-fluid lazy" alt="Kerala Tour Package" title="" src="https://ihpl.b-cdn.net/pictures/tourintro/best-of-kerala-tour-16.jpeg" style="display: block;">
										</a>
									</div>					
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>7 Nights / 8 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/best-of-kerala-tour.html">Best of Kerala</a>
												<p>Cochin – Munnar – Thekkady – Alleppey – Kovalam – Trivandrum</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Guided tour of Cochin</li>
													<li>Witness Kathakali performance</li>
													<li>Explore Tea Museum in Munnar</li>
													<li>Boat ride on Periyar Lake</li>
													<li>Cruise on backwaters on houseboat</li>
													<li>Day trip to Trivandrum<br>&nbsp;</li>
												</ul>
											</div>							
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/best-of-kerala-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Best of Kerala'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/kerala-honeymoon-package.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/kerala-honeymoon-package-257.jpeg" width="250" class="img-fluid lazy" alt="Honeymoon in Kerala Houseboat" title="Kerala Honeymoon Package for 8 Days and 7 Nights" src="https://ihpl.b-cdn.net/pictures/tourintro/kerala-honeymoon-package-257.jpeg" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>7 Nights / 8 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-honeymoon-package.html">Kerala Honeymoon Package Tour </a>
												<p>Cochin - Munnar – Thekkady – Alleppey (Houseboat) - Kovalam – Trivandrum/Cochin</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Boat ride on Periyar Lake</li>
													<li>Explore Munnar</li>
													<li>Guided tour of Cochin</li>
													<li>Stay on a luxury houseboat</li>
													<li>Enjoy at the beach</li>
													<li>Visit Padmanabhapuram Palace</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
												<div class="pkg-pricebx" style="font-size: 15px;">
													<strong>Price On Request</strong>
												</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-honeymoon-package.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Kerala Honeymoon Package Tour '); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/goa-kerala-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/goa-kerala-tour-264.jpeg" width="250" class="img-fluid lazy" alt="best beaches in kerala" title="Goa Kerala Tour" src="https://ihpl.b-cdn.net/pictures/tourintro/goa-kerala-tour-264.jpeg" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>12 Nights / 13 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/goa-kerala-tour.html">Goa Kerala Tour</a>
												<p>Goa - Cochin (Kochi) - Munnar - Periyar (Thekkady) - Kottayam - Alleppey (Alappuzha) - Kovalam - Trivandrum (Thiruvananthapuram)</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Guided tour of Old Goa churches</li>
													<li>Indulge in Ayurveda massage</li>
													<li>Sightseeing in Cochin</li>
													<li>Explore charms of Munnar</li>
													<li>Boat safari on Periyar Lake</li>
													<li>Visit Kumily spice market</li>
													<li>Backwater cruise in Kottayam-Alleppey</li>
													<li>Beach pleasures at Kovalam</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
												<div class="pkg-pricebx" style="font-size: 15px;">
													<strong>Price On Request</strong>
												</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/goa-kerala-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Goa Kerala Tour'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/kerala-backwater-tour-alleppey.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/kerala-backwater-tour-alleppey-950.jpeg" width="250" class="img-fluid lazy" alt="4 Day Kerala Backwater Tour in Alleppey" title="" src="https://ihpl.b-cdn.net/pictures/tourintro/kerala-backwater-tour-alleppey-950.jpeg" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>3 Nights / 4 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-backwater-tour-alleppey.html">4 Day Kerala Backwater Tour in Alleppey</a>
												<p>Cochin - Alleppey - Kumarakom - Cochin</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Day trip to Cochin</li>
													<li>Houseboat tour of Alleppey<br>
													&nbsp;</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-backwater-tour-alleppey.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('4 Day Kerala Backwater Tour in Alleppey'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/wayanad-tour-vythri-treehouse.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/wayanad-tour-vythri-treehouse-954.jpeg" width="250" class="img-fluid lazy" alt="3 Days Wayanad Tour with Vythiri Treehouse" title="" src="https://ihpl.b-cdn.net/pictures/tourintro/wayanad-tour-vythri-treehouse-954.jpeg" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>2 Nights / 3 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/wayanad-tour-vythri-treehouse.html">3 Days Wayanad Tour with Vythiri Treehouse</a>
												<p>Kozhikode – Wayanad - Kozhikode</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Stay in a treehouse resort</li>
													<li>Trek up to Chembra Peak</li>
													<li>Explore the heart-shaped lake</li>
													<li>Wayanad sightseeing tour<br>&nbsp;</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/wayanad-tour-vythri-treehouse.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('3 Days Wayanad Tour with Vythiri Treehouse'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/kerala-backwaters-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/kerala-backwaters-tour-244.jpeg" width="250" class="img-fluid lazy" alt="Backwater Destinations in Kerala" title="Backwater Tours in Kerala" src="https://ihpl.b-cdn.net/pictures/tourintro/kerala-backwaters-tour-244.jpeg" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>5 Nights / 6 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-backwaters-tour.html">Kerala Backwaters Tour</a>
												<p>Cochin - Alleppey - Kovalam - Trivandrum</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Sightseeing in Cochin</li>
													<li>Houseboat ride on backwaters</li>
													<li>Explore Kovalam Beach</li>
													<li>Guided tour of Trivandrum</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-backwaters-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Kerala Backwaters Tour'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">    
										<a href="https://www.indianholiday.com/tours-of-india/kerala-ayurveda-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/kerala-ayurveda-tour-269.jpeg" width="250" class="img-fluid lazy" alt="Ayurveda in Kerala" title="Kerala Ayurveda Tour" src="https://ihpl.b-cdn.net/pictures/tourintro/kerala-ayurveda-tour-269.jpeg" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>13 Nights / 14 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-ayurveda-tour.html">Kerala Ayurveda Tour</a>
												<p>Cochin - Munnar - Thekkady - Alleppey - Kovalam - Trivandrum</p>										
												<i>Trip Highlights</i>
												<ul>
													<li>Cochin sightseeing tour</li>
													<li>Guided tour of Munnar</li>
													<li>Explore Thekkady</li>
													<li>Cherish sunset cruise</li>
													<li>Visit Alleppey</li>
													<li>Enjoy at Kovalam Beach<br>&nbsp;</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-ayurveda-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Kerala Ayurveda Tour'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/oberoi-vrinda-cruise.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/oberoi-vrinda-cruise-407.png" width="250" class="img-fluid lazy" alt="Oberoi Vrinda Cruise" title="Oberoi Vrinda Cruise" src="https://ihpl.b-cdn.net/pictures/tourintro/oberoi-vrinda-cruise-407.png" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>2 Nights / 3 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/oberoi-vrinda-cruise.html">Oberoi Vrinda Cruise</a>
												<p>Allepey - Chambakulam - Kumarakom - Karumadi - Nedumudy - Kochi</p>										
												<i>Trip Highlights</i>
												<ul>
													<li>Visit Vembanad Lake</li>
													<li>Watch Kathakali dance</li>
													<li>Explore Kanjipaddam</li>
													<li>Marvel at Lord Buddha’s statue</li>
													<li>Worship at a Hindu Temple</li>
													<li>Excursion to St. Mary’s church<br>&nbsp;</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/oberoi-vrinda-cruise.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Oberoi Vrinda Cruise'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/kovalam-ayurveda-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/kovalam-ayurveda-tour-198.jpeg" width="250" class="img-fluid lazy" alt="Ayurveda in Kerala" title="Kovalam Ayurveda Tour" src="https://ihpl.b-cdn.net/pictures/tourintro/kovalam-ayurveda-tour-198.jpeg" style="display: block;">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>7 Nights / 8 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/kovalam-ayurveda-tour.html">Kovalam Ayurveda Tour</a>
												<p>Trivandrum - Kovalam - Trivandrum</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Rejuvenate with Ayurvedic massage</li>
													<li>Attend yoga session</li>
													<li>Explore Trivandrum</li>
													<li>Practice meditation</li>
													<li>Cruise on the backwaters</li>
													<li>Unwind at Kalari</li>
													<li>Marvel at stunning sunset views</li>
													<li>Have fun at beaches</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/kovalam-ayurveda-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Kovalam Ayurveda Tour'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">  
										<a href="https://www.indianholiday.com/tours-of-india/mumbai-goa-kerala-tour-package.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/mumbai-goa-kerala-tour-package-248.jpeg" width="250" class="img-fluid lazy" alt="11 Days Mumbai, Goa Kerala Package" title="" src="https://ihpl.b-cdn.net/pictures/tourintro/mumbai-goa-kerala-tour-package-248.jpeg">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>10 Nights / 11 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/mumbai-goa-kerala-tour-package.html">11 Days Mumbai, Goa Kerala Package</a>
												<p>Mumbai - Goa - Cochin (Kochi) - Munnar - Periyar (Thekkady) - Kumarakom - Alleppey (Alappuzha) – Cochin</p>
												<i>Trip Highlights</i>
												<ul>
													<li>City tour of Mumbai</li>
													<li>Visit UNESCO Elephanta Caves</li>
													<li>Sightseeing in Goa</li>
													<li>Watch Chinese Fishing Nets in Cochin</li>
													<li>Spot Nilgiri Tahr in the Eravikulam National Park</li>
													<li>Leisure walk and shopping for souvenirs in Munnar</li>
													<li>Boat ride safari at Periyar National Park</li>
													<li>Backwater cruise from Kumarakom to Alleppey</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/mumbai-goa-kerala-tour-package.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('11 Days Mumbai, Goa Kerala Package'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">    
										<a href="https://www.indianholiday.com/tours-of-india/romantic-kerala-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/romantic-kerala-tour-213.jpeg" width="250" class="img-fluid lazy" alt="Romantic Kerala Tour" title="Travel to Kerala" src="https://ihpl.b-cdn.net/pictures/tourintro/romantic-kerala-tour-213.jpeg">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>5 Nights / 6 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/romantic-kerala-tour.html">Romantic Kerala Tour</a>
												<p>Cochin – Munnar - Alleppey - Kovalam - Trivandrum</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Stay in a tree house</li>
													<li>Day trip to Munnar</li>
													<li>Explore backwaters on houseboat</li>
													<li>Have fun at beaches</li>
													<li>Excursion to Trivandrum</li>
													<li>Visit Eravikulam National Park<br>&nbsp;</li>
												</ul>
											</div>
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/romantic-kerala-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Romantic Kerala Tour'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="https://www.indianholiday.com/tours-of-india/hill-stations-south-india.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/hill-stations-south-india-772.jpeg" width="250" class="img-fluid lazy" alt="Hill Stations of South India" title="" src="https://ihpl.b-cdn.net/pictures/tourintro/hill-stations-south-india-772.jpeg">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>5 Nights / 6 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/hill-stations-south-india.html">Hill Stations of South India</a>
												<p>Bangalore - Coorg - Mysore - Wayanad - Kozhikode</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Half day trip to Coorg</li>
													<li>Explore Mysore Palace</li>
													<li>Visit temple on Chamundi Hill</li>
													<li>Wayanad city tour</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/hill-stations-south-india.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Hill Stations of South India'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">  
										<a href="https://www.indianholiday.com/tours-of-india/wayanad-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/wayanad-tour-748.png" width="250" class="img-fluid lazy" alt="Short Escape to Wayanad" title="" src="https://ihpl.b-cdn.net/pictures/tourintro/wayanad-tour-748.png">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>2 Nights / 3 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/wayanad-tour.html">Short Escape to Wayanad</a>
												<p>Kozhikode - Wayanad</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Trek to the Chembra Peak</li>
													<li>Explore a heart-shaped lake</li>
													<li>Visit Banasura Sagar Dam</li>
													<li>Sightseeing of Edakkal Caves<br>&nbsp;</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/wayanad-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Short Escape to Wayanad'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box"> 
										<a href="https://www.indianholiday.com/tours-of-india/best-of-kerala-with-spice-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/best-of-kerala-with-spice-tour-145.jpeg" width="250" class="img-fluid lazy" alt="Backwater Cruising and Bird Watching in Kerala" title="Best of Kerala with Spice Tour" src="https://ihpl.b-cdn.net/pictures/tourintro/best-of-kerala-with-spice-tour-145.jpeg">
										</a>
									</div>							
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>8 Nights / 9 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/best-of-kerala-with-spice-tour.html">Best of Kerala with Spice Tour</a>
												<p>Cochin - Munnar - Periyar - Kumarakom - Alleppey - Kovalam - Trivandrum</p>
												<i>Trip Highlights</i>
												<ul>
													<li>Visit the Paradesi Synagogue</li>
													<li>Excursion to Periyar</li>
													<li>Stay in a houseboat</li>
													<li>Boat Safari on Periyar Lake</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/best-of-kerala-with-spice-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Best of Kerala with Spice Tour'); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
																   
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">    
										<a href="https://www.indianholiday.com/tours-of-india/kerala-offbeat-tour.html" class="pkg-imgbx">
											<img data-original="https://ihpl.b-cdn.net/pictures/tourintro/kerala-offbeat-tour-935.jpeg" width="250" class="img-fluid lazy" alt="Offbeat  Kerala Tour " title="" src="https://ihpl.b-cdn.net/pictures/tourintro/kerala-offbeat-tour-935.jpeg">
										</a> 
									</div>
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>6 Nights / 7 Days</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-offbeat-tour.html">Offbeat  Kerala Tour </a>
												<p>Wayanad - Kannur - Kasaragod </p>
												<i>Trip Highlights</i>
												<ul>
													<li>Enjoy a homestay in Wayanad</li>
													<li>Explore Thrikkaipetta</li>
													<li>Feast on magnificent views</li>
													<li>Homestay at a Private Island<br>&nbsp;</li>
												</ul>
											</div>									
											<div class="col-sm-3 txt-cntr">
												<span>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong>Price On Request</strong>
													</div>
												</span>
												<a href="https://www.indianholiday.com/tours-of-india/kerala-offbeat-tour.html" class="pkglinks-view text-center">View Details</a>
												<a href="#" onclick="doEnquiry('Offbeat  Kerala Tour '); return false;" class="pkglinks-enquire text-center">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection