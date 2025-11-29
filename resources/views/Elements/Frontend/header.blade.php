<header class="header-area">
<!-- Top Header Area -->
<div class="top-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="top-header-content">
					<!-- Top Headline -->
					<div class="top-headline headline_left">					
						<div class="single-contact-info" >
							<p style="color:#1E1755 !important">
    <a href="mailto:<?php echo @\App\ThemeOption::where('meta_key','email')->first()->meta_value; ?>" style="color:#1E1755 !important; text-decoration: none; font-size:12px;font-weight: 400;">
        <i class="fa fa-envelope-o" style="color:#1E1755 !important"></i> 
        <?php echo @\App\ThemeOption::where('meta_key','email')->first()->meta_value; ?>
    </a>
</p>
							<p style="color:#1E1755 !important" >
  <i class="fa fa-phone" style="color:#1E1755 !important"></i> 
  <a href="tel:<?php echo @\App\ThemeOption::where('meta_key','phone')->first()->meta_value; ?>" 
     style="color:#1E1755 !important; text-decoration: none;font-size:12px;font-weight: 400;;" 
     onmouseover="this.style.color='#FF5733'" onmouseout="this.style.color='#1E1755'">
    <?php echo @\App\ThemeOption::where('meta_key','phone')->first()->meta_value; ?>
  </a>
</p>						
						</div>   
					</div>  
					<!-- Top Login & Faq & Earn Monery btn -->  
					<div class="top-headline headline_right"> 
						<div class="single-contact-info">
							<ul>
								<li><a style="color:#1E1755 !important" href="<?php echo URL::to('/'); ?>/book-an-appointment">Book An Appointment</a></li>								
								<li><a style="color:#1E1755 !important" href="<?php echo URL::to('/'); ?>/contact-us">Contact</a></li>                             
              <!-- <li><a style="color:#1E1755 !important" href="<?php echo @\App\ThemeOption::where('meta_key','facebook')->first()->meta_value; ?>"><i class="fa fa-facebook"></i></a></li> --!>
              <!--  <li><a style="color:#1E1755 !important" href="<?php echo @\App\ThemeOption::where('meta_key','twitter')->first()->meta_value; ?>"><i class="fa-brands fa-x-twitter"></i></a></li> --!>
                               <!-- <li><a href="<?php echo URL::to('/'); ?>/blogs">Blog</a></li>  --!>
							
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<!-- Navbar Area -->
<div class="cryptos-main-menu">
	<div class="classy-nav-container breakpoint-off light left">
		<div class="container-fluid">
			<!-- Menu -->
			<nav class="classy-navbar justify-content-between" id="cryptosNav">
				<!-- Logo -->
				<a class="nav-brand" href="<?php echo URL::to('/'); ?>">
					<img src="{{asset('public/img/logo_img')}}/<?php echo @\App\ThemeOption::where('meta_key','logo')->first()->meta_value; ?>" alt="">
				</a>
				<!-- Navbar Toggler -->
				<div class="classy-navbar-toggler">
					<span class="navbarToggler"><span></span><span></span><span></span></span>
				</div>
				<!-- Menu -->
				<div class="classy-menu">
					<!-- close btn -->
					<div class="classycloseIcon">
						<div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
					</div>
					<!-- Nav Start -->
					<div class="classynav">
						<ul>
							<li class="megamenu-item"><a href="#">Study in Australia</a>
								<div class="megamenu">
									<ul class="single-mega cn-col-3">
										<li><h6>&nbsp;&nbsp;EDUCATION</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/admission-in-australia">- Admission in Australia</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/new-coe">- New Coe</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/course-change-in-australia">- Course Change  in Australia</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/what-is-recognized-prior-learning-rpl-australia">- What is Recognized Prior Learning (RPL), Australia?</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/professional-year-program">- Professional Year Program</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/request-to-defer-your-studies">- Request to Defer Your Studies</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/our-affiliations">- Our Affiliations</a></li>
									</ul>
									<ul class="single-mega cn-col-3">
										<li><h6>&nbsp;&nbsp;Student Visa</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-subsequent-entrant-visa-information">- Student Dependent Visa (Subclass 500)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-visa-extension">- Student visa extension</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-guardian-visa-subclass-590">- Student Guardian Visa Subclass 590</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-visa-journey">- Student visa Journey</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-extension">- Student Extension</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-subsequent-visa-to-student-visa">- Student Subsequent visa to Student visa</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-visa-information">- Student Visa Information</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/training-visa-subclass-407">- Training Visa (Subclass 407)</a></li>
									</ul>
									<ul class="single-mega cn-col-3">
										<li><h6>&nbsp;&nbsp;Other</h6></li>
									
										<li><a href="<?php echo URL::to('/'); ?>/student-visa-financial-calculator">- Student visa financial calculator</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/tourist-to-student-visa">- Tourist to Student visa</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/master-to-diploma-student-visa-subclass-500">- Master to Diploma Student visa Subclass 500</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/student-dependent-visa-to-student-visa-subclass-500">- Student dependent visa to Student Visa (Subclass 500)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/apply-afp-ipc-form/">- Apply AFP / IPC Form</a></li>
									</ul>
								</div>
								<span class="dd-trigger"></span>
							</li>	 
							<li class="cn-dropdown-item has-down"><a href="#">Visitor Visa</a>
								<ul class="dropdown">
									<li><a href="<?php echo URL::to('/'); ?>/visitor-visa">VISITOR VISA(Subclass 600)</a>
										<ul class="dropdown">
											<li><a href="<?php echo URL::to('/'); ?>/australian-visitor-visa-offshore">Offshore tourist visa extension (Outside Australia)</a></li>
											<li><a href="<?php echo URL::to('/'); ?>/travel-exemption-to-australia-during-covid-19">Travel exemption</a></li>
										</ul>	
									</li>
									<li><a href="<?php echo URL::to('/'); ?>/work-and-holiday-visa-subclass-462">Work and Holiday Visa Subclass 462</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/work-and-holiday-visa-subclass-417">Work and Holiday Visa Subclass 417</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/sponsored-family-stream">Sponsored family stream</a></li>
								</ul>
								<span class="dd-trigger"></span>
							</li>	
							<li class="megamenu-item"><a href="#">Migration</a>
								<div class="megamenu">
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Graduate visa</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/temporary-graduate-visa-subclass-485">- Temporary Graduate visa (subclass 485)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/post-study-work-visa-subclass-485">- Post Study Work visa (subclass 485)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/skilled-recognised-graduate-visa-subclass-476">- Skilled-Recognised Graduate visa (subclass 476)</a></li>
									</ul>
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Permanent Visa</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/skilled-independent-visa-subclass-189">- Skilled Independent visa (subclass 189)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/skilled-nominated-visa-subclass-190">- Skilled Nominated visa (subclass 190)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/skilled-regional-visa-subclass-887">- Skilled Regional visa (subclass 887)</a></li>
									</ul>
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Regional Visas</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/permanent-residence-skilled-regional-visa-subclass-191">- Permanent Residence (Skilled Regional) visa (subclass 191)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/skilled-work-regional-provisional-visa-subclass-491">- Skilled Work Regional (Provisional) visa (subclass 491)</a></li>
									</ul>
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Skill Assessment</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/acs-skill-assessment">- ACS skill assessment</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/vetassess-skill-assessment">- VETASSESS skill assessment</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/job-ready-program">- Job Ready program</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/ea-skill-assessment">- EA Skill Assessment</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/accountant-skill-assessment">- Accountant skill assessment</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/nursing-aphara-registration-and-anmac-skill-assessment">- Nursing APHARA registration and ANMAC skill assessment</a></li>
									</ul>
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Others</h6></li>
									
										<li><a href="<?php echo URL::to('/'); ?>/pr-point-calculator">- PR Point Calculator</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/how-to-claim-regional-points">- How to claim regional points</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/points-for-english-score">- Points for English score</a></li>
									</ul>
								</div>
								<span class="dd-trigger"></span>
							</li>
							<li class="megamenu-item"><a href="#">Family visa</a>
								<div class="megamenu">
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Partner visa</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/partner-provisional-visa-subclass-309">- Partner Provisional visa (subclass 309)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/partner-permanent-visa-subclass-100">- Partner Permanent visa (Subclass 100)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/partner-provisional-visa-subclass-820">- Partner Provisional visa (subclass 820)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/partner-permanent-visa-subclass-801">- Partner Permanent visa (Subclass 801)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/prospective-marriage-visa-subclass-300">- Prospective Marriage visa (subclass 300)</a></li>
									</ul>
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Parents Visa</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/contributory-aged-parent-temporary-visa-subclass-884">- Contributory Aged Parent (Temporary) visa (subclass 884)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/contributory-aged-parent-visa-subclass-864">- Contributory Aged Parent visa (subclass 864)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/contributory-parent-temporary-visa-subclass-173">- Contributory Parent (Temporary) visa (subclass 173)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/contributory-parent-visa-subclass-143">- Contributory Parent visa (subclass 143)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/parent-visa-subclass-103">- Parent visa (subclass 103)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/aged-parent-visa-subclass-804">- Aged Parent visa (subclass 804)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/sponsored-parent-temporary-visa-subclass-870">- Sponsored Parent (Temporary) visa (subclass 870)</a></li>
									</ul> 
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Child Visas</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/child-visa-subclass-101">- Child Visa Subclass 101</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/child-visa-subclass-802">- Child Visa Subclass 802</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/adoption-visa-in-australia">- Adoption Visa in Australia</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/remaining-relative-visa-subclass-115">- Remaining relative visa (subclass 115)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/remaining-relative-visa-subclass-835">- Remaining Relative Visa Subclass 835</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/orphan-reletive-visa-117">- Orphan Relative Visa 117</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/reletive-visa-837">- Orphan Relative Visa Subclass 837</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/dependent-child-visa">- Dependent Child Visa</a></li>
									</ul>
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Relative visas</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/aged-dependent-relative-visa-subclass-114">- Aged Dependent Relative visa (subclass 114)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/aged-dependent-relative-visa-offshore-subclass-114">- Aged Dependent Relative VisaOffshore (subclass 114)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/aged-dependent-relative-visa-onshore-subclass-838">- Aged Dependent Relative Visa Onshore (subclass 838)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/carer-visa-offshore-subclass-116">- Carer visa Offshore (subclass 116)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/carer-visa-onshore-subclass-836">- Carer visa Onshore (subclass 836)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/remaining-relative-visa-subclass-115">- Remaining relative visa (subclass 115)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/remaining-relative-visa-subclass-835">- REMAINING RELATIVE VISA SUBCLASS 835</a></li>
									</ul>
									<ul class="single-mega cn-col-5">
										<li><h6>&nbsp;&nbsp;Others</h6></li>
										
										<li><a href="<?php echo URL::to('/'); ?>/cost-for-contributory-visas">- Cost for contributory visas</a></li>
									</ul>
								</div>
								<span class="dd-trigger"></span>
							</li>
							<li class="megamenu-item"><a href="#">Employee sponsored visas</a>
								<div class="megamenu">
									<ul class="single-mega cn-col-4">
										<li><h6>&nbsp;&nbsp;Temporary Visas</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/temporary-skill-shortage-visa-subclass-482">- Temporary Skill Shortage visa (subclass 482)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/designated-area-migration-agreements-dama">- Designated Area Migration Agreements (DAMA)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/skilled-employer-sponsored-regional-provisional-visa-subclass-494">- Skilled Employer Sponsored Regional (provisional) visa (subclass 494)</a></li> 
										<li><a href="<?php echo URL::to('/'); ?>/temporary-activity-visa-subclass-408">- Temporary Activity visa (subclass 408)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/temporary-work-short-stay-specialist-visa-subclass-400">- Temporary Work (Short Stay Specialist) visa (subclass 400)</a></li>
									</ul>
									<ul class="single-mega cn-col-4">
										<li><h6>&nbsp;&nbsp;Permanent Visas</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/employer-nomination-scheme-subclass-186-trt">- Employer Nomination Scheme (subclass 186) TRT</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/employer-nomination-direct-entry-subclass-186">- Employer Nomination Direct Entry (subclass 186)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/permanent-residence-skilled-regional-visa-subclass-191">- Permanent Residence (Skilled Regional) visa (subclass 191)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/distinguished-talent-visa-offshore-subclass-124">- Distinguished Talent visa Offshore (subclass 124)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/distinguished-talent-visa-onshore-subclass-858">- Distinguished Talent visa Onshore (subclass 858)</a></li>
									</ul>
									<ul class="single-mega cn-col-4">
										<li><h6>&nbsp;&nbsp;Global Talent visas</h6></li>
										<li><a href="<?php echo URL::to('/'); ?>/global-talent-independent-program-gti">- Global Talent Independent program (GTI)</a></li>
										<li><a href="<?php echo URL::to('/'); ?>/global-talent-employer-sponsored-gtes">- Global Talent Employer Sponsored (GTES)</a></li>
									</ul>
									<ul class="single-mega cn-col-4">
										<li><h6>&nbsp;&nbsp;Others</h6></li>
									
									</ul>
								</div>
								<span class="dd-trigger"></span>
							</li>
							<li class="cn-dropdown-item has-down"><a href="#">Business visas</a>
								<ul class="dropdown">
									<li><a href="<?php echo URL::to('/'); ?>/business-innovation-and-investment-permanent-visa-subclass-888">Business Innovation and Investment (permanent) visa (subclass 888)</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/business-innovation-and-investment-provisional-visa-subclass-188">Business Innovation and Investment (provisional) visa (subclass 188)</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/business-talent-permanent-visa-subclass-132">Business Talent (Permanent) visa (subclass 132)</a></li>
									
								</ul> 
								<span class="dd-trigger"></span>
							</li>
							<li class="cn-dropdown-item has-down"><a href="#">Appeals</a>
								<ul class="dropdown">
									<li><a href="<?php echo URL::to('/'); ?>/visa-refusal">Visa refusal</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/visa-cancellation">Visa cancellation</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/notice-of-intention-to-consider-cancellation">Notice of intention to Consider Cancellation</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/waiver-request">Waiver request</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/applying-for-work-rights">Applying for Work Rights</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/applying-for-study-rights">Applying for Study Rights</a></li>
								</ul>
								<span class="dd-trigger"></span>
							</li>
							<li class="cn-dropdown-item has-down"><a href="#">Citizenships</a>
								<ul class="dropdown">
									<li><a href="<?php echo URL::to('/'); ?>/citizenship-by-conferral">Citizenship by Conferral</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/citizenship-by-descent">Citizenship by Descent</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/evidence-of-australian-citizenship">Evidence of Australian Citizenship</a></li> 
								</ul>
								<span class="dd-trigger"></span>
							</li>
							<li class="cn-dropdown-item has-down"><a href="#">Other Countries</a>
								<ul class="dropdown">
									<li><a href="<?php echo URL::to('/'); ?>/canada">CANADA</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/new-zealand">New Zealand</a></li>
									<li><a href="<?php echo URL::to('/'); ?>/usa">USA</a></li>
								</ul>
								<span class="dd-trigger"></span>
							</li>
						</ul>
					</div> 
					<!-- Nav End -->
				</div>
			</nav>
		</div>
	</div>
</div>
</header>
          
          <nav class="custom-nav">
    <ul style="Merienda, cursive;font-optical-sizing: auto;font-style: normal;font-weight:100">
        <li class="social"><a href="<?php echo @\App\ThemeOption::where('meta_key','facebook')->first()->meta_value; ?>"><i class="fa-brands fa-facebook"></i><span>Facebook</span></a></li>
      <li class="social"><a href="https://www.instagram.com/bansalimmigrationconsultants"><i class="fa-brands fa-instagram"></i><span>Instagram</span></a></li>
        <li class="social"><a href="https://www.threads.net/@bansalimmigrationconsultants?hl=en"><i class="fa-brands fa-threads"></i><span>Thread</span></a></li>        
        <li class="social"><a href="https://www.linkedin.com/company/18215774/admin/dashboard/"><i class="fa-brands fa-linkedin"></i><span>LinkedIn</span></a></li>
        <li class="social"><a href="https://youtube.com/@bansalimmigrationconsultants?si=-NXX1LFJKp5VcN1j"><i class="fa-brands fa-youtube"></i><span>YouTube</span></a></li>
    </ul>
</nav>
         <div class="custom-open-form" style="Merienda, cursive;font-optical-sizing: auto;font-style: normal;">Request Call Back</div>

    <div class="custom-form-popup">
        <span class="close-btn"><i class="fa-solid fa-xmark"></i></span>
        <h3 style="Merienda, cursive;font-optical-sizing: auto;font-style: normal;">Contact Us</h3>
        <form>
            <div style="position: relative;">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div style="position: relative;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div style="position: relative;">
                <label for="mobile">Mobile:</label>
                <input type="tel" id="mobile" name="mobile" required>
            </div>
            <div style="position: relative;">
                <label for="message">Your Query:</label>
                <textarea id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
        <script>
         document.querySelector('.custom-open-form').addEventListener('click', function() {
            document.querySelector('.custom-form-popup').style.display = 'block';
        });

        document.querySelector('.close-btn').addEventListener('click', function() {
            document.querySelector('.custom-form-popup').style.display = 'none';
        });
    </script>