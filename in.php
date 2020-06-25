﻿<?php
ob_start();
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$res = $conn->query("SELECT * FROM users WHERE id=" . $_SESSION['user']);
$userRow = mysqli_fetch_array($res, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
		<!-- BASICS -->
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Go Detection</title>
        <meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="css/isotope.css" media="screen" />	
		<link rel="stylesheet" href="js/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/bootstrap-theme.css">
        <link rel="stylesheet" href="css/style.css">
		<!-- skin -->
		<link src="skin/default.css">
        <a href="#header" class="scrollup"><i class="fa fa-chevron-up"></i></a> 

    <script src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyASm3CwaK9qtcZEWYa-iQwHaGi3gcosAJc&sensor=false"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/jquery.nicescroll.min.js"></script>
    <script src="js/fancybox/jquery.fancybox.pack.js"></script>
    <script src="js/skrollr.min.js"></script>       
    <script src="js/jquery.scrollTo-1.4.3.1-min.js"></script>
    <script src="js/jquery.localscroll-1.2.7-min.js"></script>
    <script src="js/stellar.js"></script>
    <script src="js/jquery.appear.js"></script>
    <script src="js/validate.js"></script>
    <script src="js/main.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    
        <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<script src="js/jquery.tagcanvas.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/stopwords.js"></script>
<script type="text/javascript" src="js/lda.js"></script>
<script>

function topicise() {
    //console.log("analysing "+sentences.length+" sentences...");
    var documents = new Array();
    var f = {};
    var vocab=new Array();
    var docCount=0;
    for(var i=0;i<sentences.length;i++) {
        if (sentences[i]=="") continue;
        var words = sentences[i].split(/[\s,\"]+/);
        if(!words) continue;
        var wordIndices = new Array();
        for(var wc=0;wc<words.length;wc++) {
            var w=words[wc].toLowerCase().replace(/[^a-z\'A-Z0-9 ]+/g, '');
            //TODO: Add stemming
            if (w=="" || w.length==1 || stopwords[w] || w.indexOf("http")==0) continue;
            if (f[w]) { 
                f[w]=f[w]+1;            
            } 
            else if(w) { 
                f[w]=1; 
                vocab.push(w); 
            };  
            wordIndices.push(vocab.indexOf(w));
        }
        if (wordIndices && wordIndices.length>0) {
            documents[docCount++] = wordIndices;
        }
    }
        
    var V = vocab.length;
    var M = documents.length;
    var K = parseInt($( "#topics" ).val());
    var alpha = 0.1;  // per-document distributions over topics
    var beta = .01;  // per-topic distributions over words

    lda.configure(documents,V,10000, 2000, 100, 10);
    lda.gibbs(K, alpha, beta);

    var theta = lda.getTheta();
    var phi = lda.getPhi();

    var text = '';

    //topics
    var topTerms=20;
    var topicText = new Array();
    for (var k = 0; k < phi.length; k++) {
        text+='<canvas id="topic'+k+'" class="topicbox color'+k+'"><ul>';
        var tuples = new Array();
        for (var w = 0; w < phi[k].length; w++) {
             tuples.push(""+phi[k][w].toPrecision(2)+"_"+vocab[w]);
        }
        tuples.sort().reverse();
        if(topTerms>vocab.length) topTerms=vocab.length;
        topicText[k]='';

        for (var t = 0; t < topTerms; t++) {
            var topicTerm=tuples[t].split("_")[1];
            var prob=parseInt(tuples[t].split("_")[0]*100);
            if (prob<0.0001) continue;
            text+=( '<li><a href="javascript:void(0);" data-weight="'+(prob)+'" title="'+prob+'%">'+topicTerm +'</a></li>' );           
           // console.log("topic "+k+": "+ topicTerm+" = " + prob  + "%");
            var myTextArea = document.getElementById('myArea');
            myTextArea.innerHTML += "topic "+k+": "+ topicTerm+" = " + prob  + "%\n"  ;
            topicText[k] += ( topicTerm +" ");
        }
        text+='</ul></canvas>';
    }
    //$('#topiccloud').html(text);
    
    // text='<div class="spacer"> </div>';
    // //highlight sentences   
    // for (var m = 0; m < theta.length; m++) {
    //     text+='<div class="lines">';
    //     text+='<div style="display:table-cell;width:100px;padding-right:5px">';
    //     for (var k = 0; k < theta[m].length; k++) {
    //         text+=('<div class="box bgcolor'+k+'" style="width:'+parseInt(""+(theta[m][k]*100))+'px" title="'+topicText[k]+'"></div>');
    //     }
    //     text+='</div>'+sentences[m]+'</div>';
    // }
    // $("#output").html(text);
    
    for (var k = 0; k < phi.length; k++) {
        if(!$('#topic'+k).tagcanvas({
              textColour: $('#topic'+k).css('color'),
              maxSpeed: 0.05,
             initial: [(Math.random()>0.5 ? 1: -1) *Math.random()/2,(Math.random()>0.5 ? 1: -1) *Math.random()/2],  //[0.1,-0.1],
              decel: 0.98,
              reverse: true,
              weightSize:10,
              weightMode:'size',
              weightFrom:'data-weight',
              weight: true
            })) 
        {
            $('#topic'+k).hide();
        } else {
            //TagCanvas.Start('topic'+k);
        }
    }
}

$(document).ready(function(){
    var select = $( "#topics" );
    var slider = $( "<div id='slider'></div>" ).insertAfter( select ).slider({
        min: 2,
        max: 10,
        range: "min",
        value: select[0].selectedIndex+2,
        slide: function( event, ui ) {
            select[0].selectedIndex = ui.value-2;
        }
    });
    $( "#topics" ).change(function() {
        slider.slider( "value", this.selectedIndex + 2 );
    });
});

function btnTopiciseClicked() {
    $('#btnTopicise').attr('disabled','disabled');
    sentences = $('#text').val().split("\n");
    topicise();
    $('#btnTopicise').removeAttr('disabled');

    
}

var sentences;
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-50176069-1', 'awaisathar.github.io');
  ga('send', 'pageview');

</script>
</head>
    <body>
		<section id="header" class="appear"></section>
		<div class="navbar navbar-fixed-top" role="navigation" data-0="line-height:100px; height:100px; background-color:rgba(0,0,0,0.3);" data-300="line-height:60px; height:60px; background-color:rgba(0,0,0,1);">
			 <div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="fa fa-bars color-white"></span>
					</button>
					<h1 style="margin-top:-20px"><a class="navbar-brand" href="index.html" data-0="line-height:90px;" data-300="line-height:50px;">Go Detection
					</a></h1>
				</div>
				<div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav" data-0="margin-top:20px;" data-300="margin-top:5px;">
                        <li class="active"><a href="index.html">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#section-works">Go Detection</a></li>
                        <li><a href="#section-contact">Contact</a></li>
                        <li>
                            

 <ul class="nav navbar-nav navbar-right">

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">
                        <span
                            class="glyphicon glyphicon-user"></span>&nbsp;Logged
                        in: <?php echo $userRow['email']; ?>
                        &nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="logout.php?logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        



                        </li>
                    </ul>
				</div><!--/.navbar-collapse -->
			</div>
		</div>
        <
        
            
            <!--Slides-->
            <div class="carousel-inner" role="listbox">
                <!-- First slide -->
                <div class="carousel-item active">
                    <!--Mask color-->
                    <div class="view">
                        <!--Video source-->
                        <video class="video-fluid" autoplay loop muted>
                            <source src="video3IE.mp4" type="video/mp4" />
                        </video>
                        <div class="mask rgba-black-strong"></div>
                    </div>

                    <!--Caption-->
                    <div class="carousel-caption">
                        <div class="animated fadeInDown">
                            <h1 style="margin-top:-1700px; font-size:100px;color:white" class="h3-responsive ">
                                Go Detection: <br /> <br/> <br/> <br/>
                                Detection on <br/> <br/><br/> <br/>
                                the brink <br/> <br/> <br/>
                            </h1>
                        </div>
                    </div>
                    <!--Caption-->
                </div>

                <!-- services -->
                <section id="section-services" class="section pad-bot30 bg-white">
                    <div class="container">

                        <div class="row mar-bot40">
                            <div class="col-lg-4">
                                <div class="align-center">
                                     <img src="images/p.png" style="height:150px;width:150px" />
                                    <h4 class="text-bold">Social Media Monitoring</h4>
                                    <p>
                                        Every day, people send 500 million tweets. Impressive, right?
                                        And that is just Twitter! In this immense volume of data generated on
                                        social media, there are mentions of products or services, stories of customer
                                        experiences and interactions between users and brands. For
                                        companies, following these conversations is vital to get real-time actionable insights
                                        from customers, address potential issues or anticipate a crisis. However,
                                        analyzing this data manually is not a feasible task.

                                        Topic analysis allows you to automatically add relevant context to the data obtained
                                        through social media, in order to understand what people are actually saying about your brand.
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="align-center">
                                    <img src="images/1.png" style="height:150px;width:150px" />
                                    <h4 class="text-bold">Knowledge Management</h4>
                                    <p>
                                        Organizations generate a huge amount of data every day. In this context, knowledge management aims to provide the means to capture,
                                        store, retrieve and share that data when needed. Topic detection has enormous potential when it
                                        comes to analyzing large data sets and extracting the most relevant information out of them.

                                        This could transform industries like healthcare, where tons of complex data is produced
                                        every second ? and it is expecting to see an explosive growth in the next few years ? but is hard
                                        to access it at the right time.
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="align-center">
                                    <img src="images/financial.png" style="height:150px;width:150px" />
                                    <h4 class="text-bold">Business Intelligence</h4>
                                    <p>
                                        This is the era of data. Collecting and analyzing data from different sources (the so-called Business Intelligence) creates unique opportunities for businesses. By taking advantage of insightful and actionable information, companies are able to improve their decision-making processes, stand out from their competitors, identify trends and spot problems before they escalate.

                                        When it comes to market research and competitive analysis, artificial intelligence (AI) can come to the rescue! You can use topic analysis to
                                        sift through product reviews of your brand and compare them with those that mention your competition.
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>
                </section>

                <!-- spacer section:testimonial -->
                <section id="about">
                <section id="testimonials" class="section" data-stellar-background-ratio="0.5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="align-center">
                                    <div class="testimonial pad-top40 pad-bot40 clearfix">
                                        <h5><b>
                                                Go detection tool is a system to detect topic
                 from a collection of document. We use an efficient method to discover topic in a collection of
    documents known as topic model. A topic model is a type of statistical model for discovering
    topics from collection of documents.System will extract keywords which occur
    often and will cluster this keywords using clustering algorithm and will detect topic from a
    collection of documents. This system takes co occurrence of terms into account which gives
    best result. This system can be useful for web crawlers and for web users.
</h5></b>
                                        <br />
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
            </div>	
		</section>
		</section>	
		<!-- about -->
		<section id="section-about" class="section appear clearfix" style="background-image:url(images/blue.jpg)">
		<div class="container" >

				<div class="row mar-bot40">
					<div class="col-md-offset-3 col-md-6">
						<div class="section-header"> <br/>
							<h1 class="section-heading animated" data-animation="bounceInUp" style="font-size:60px">Our Team</h1>
							<p style="color:black; font-size:16px;"><b>Teamwork is the ability to work together toward a common vision. The ability to direct 
							individual accomplishments toward organizational objectives. It is the fuel that allows common people 
							to attain uncommon results.</b></p></div>
					</div>
				</div>

					<div class="row align-center mar-bot40">
						<div class="col-md-4">
							<div class="team-member">
								<figure class="member-photo"><img src="img/team/member1.jpg" alt="" /></figure>
								<div class="team-detail">
									<h3> <b>Anjali Sharma</b></h3>
                                    <span><font color="black"><b>Web developer and Web designer</b></font></span>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="team-member">
								<figure class="member-photo"><img src="img/team/member2.jpg" alt="" /></figure>
								<div class="team-detail">
									<h3><b>Kavya Vaish</b></h3>
                                    <span><font color="black"><b>Web developer and Web designer</b></font></span>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="team-member">
								<figure class="member-photo"><img src="img/team/member3.jpg" alt="" /></figure>
								<div class="team-detail">
								<h3><b>Hemant Ravi</b></h3>
									<span ><font color="black"><b>Web developer and Web designer</b></font></span>
								</div>
							</div>
						</div>
						
						
					</div>
						
		</div>
		</section>
		<!-- /about -->
		
		
		
		<!-- section works -->
		<section id="section-works" class="section appear clearfix">
            <div class="container" >

                <div class="row mar-bot40" >
                    <div class="col-md-offset-3 col-md-6">
                        <div class="section-header">
                            <h2 class="section-heading animated" data-animation="bounceInUp"> GO DETECTION</h2>

                        </div>
                    </div>
                </div>

                <div class="container">
                    <textarea id="text" style="width:1100px; height:250px">  </textarea>
                </div>
                <br />
                <div class="container">
                    
                <div id="menu">
<label for="topics">Topics:</label>
<select name="topics" id="topics">
    <option>2</option>
    <option>3</option>
    <option  selected="selected">4</option>
    <option>5</option>
    <option>6</option>
    <option>7</option>
    <option>8</option>
    <option>9</option>
    <option>10</option>
</select><br/><input id="btnTopicise" type="button" onclick="btnTopiciseClicked();" value="Analyse"/><br/>
</div>
<div class="spacer"> </div>
<div id="topiccloud"></div>
<br/>
<div id="output">
</div>
                <div class="container">
                    <textarea style="width:1100px; height:100px" id="myArea">  </textarea>
                </div>
</section>
		<section id="parallax2" class="section parallax" data-stellar-background-ratio="0.5" >	
            <div class="align-center pad-top40 pad-bot40" >
                <blockquote class="bigquote color-white"></blockquote>
			
            </div>
		</section>

		<!-- contact -->
		<section id="section-contact" class="section appear clearfix">
			<div class="container">
				
				<div class="row mar-bot40  " >
					<div class="col-md-offset-3 col-md-6" >
						<div class="section-header">
							<h2 class="section-heading animated" data-animation="bounceInUp">Contact us</h2>
							
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<div class="cform" id="contact-form">
							<div id="sendmessage">tact
								 Your message has been sent. Thank you!
							</div>
							<form action="contact/contact.php" method="post" role="form" class="contactForm">
							  <div class="form-group">
								<label for="name">Your Name</label>
								<input type="text" name="name" class="form-control" id="name" placeholder="Your Name" data-rule="maxlen:4" data-msg="Please enter at least 4 chars" />
								<div class="validation"></div>
							  </div>
							  <div class="form-group">
								<label for="email">Your Email</label>
								<input type="email" class="form-control" name="email" id="email" placeholder="Your Email" data-rule="email" data-msg="Please enter a valid email" />
								<div class="validation"></div>
							  </div>
							  <div class="form-group">
								<label for="subject">Subject</label>
								<input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" data-rule="maxlen:4" data-msg="Please enter at least 8 chars of subject" />
								<div class="validation"></div>
							  </div>
							  <div class="form-group">
								<label for="message">Message</label>
								<textarea class="form-control" name="message" rows="5" data-rule="required" data-msg="Please write something for us"></textarea>
								<div class="validation"></div>
							  </div>
							  
							  <button type="submit" class="btn btn-theme pull-left">SEND MESSAGE</button>
							</form>

						</div>
					</div>
					<!-- ./span12 -->
				</div>
				
			</div>
		</section>

	<section id="footer" class="section footer">
		<div class="container">
			<div class="row animated opacity mar-bot20" data-andown="fadeIn" data-animation="animation">
				<div class="col-sm-12 align-center">
                    <ul class="social-network social-circle">
                        <li><a href="#" class="icoRss" title="Rss"><i class="fa fa-rss"></i></a></li>
                        <li><a href="#" class="icoFacebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#" class="icoTwitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#" class="icoGoogle" title="Google +"><i class="fa fa-google-plus"></i></a></li>
                        <li><a href="#" class="icoLinkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
                    </ul>				
				</div>
			</div>

			
		</div>

	</section>
	    	</body>
</html>