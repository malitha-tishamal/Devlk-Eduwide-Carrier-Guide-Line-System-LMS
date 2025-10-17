<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerPath - Professional Career Guidance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #1a73e8;
            --primary-dark: #0d47a1;
            --primary-light: #e8f0fe;
            --accent: #4285f4;
            --white: #ffffff;
            --light-gray: #f5f7fa;
            --text: #333333;
            --text-light: #5f6368;
            --success: #34a853;
            --warning: #f9ab00;
        }

        body {
            background-color: var(--light-gray);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .logo i {
            font-size: 2.2rem;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        nav a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        nav a:hover {
            color: var(--primary);
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s;
        }

        nav a:hover::after {
            width: 100%;
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: var(--primary-light);
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        /* Assessment Section */
        .assessment {
            background-color: var(--white);
            padding: 60px 0;
            margin: 40px 0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary);
            display: inline-block;
            position: relative;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--primary);
            border-radius: 2px;
        }

        .assessment-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .assessment-card {
            background-color: var(--light-gray);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--primary);
        }

        .assessment-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .assessment-icon {
            width: 70px;
            height: 70px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary);
            font-size: 1.8rem;
        }

        .assessment-card h3 {
            margin-bottom: 15px;
            color: var(--text);
        }

        /* Career Paths */
        .career-paths {
            margin: 60px 0;
        }

        .path-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .path-tab {
            padding: 12px 25px;
            background-color: var(--white);
            border: 2px solid var(--primary-light);
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .path-tab.active {
            background-color: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .path-content {
            display: none;
        }

        .path-content.active {
            display: block;
        }

        .path-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .path-card {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .path-card:hover {
            transform: translateY(-5px);
        }

        .path-header {
            background-color: var(--primary);
            color: var(--white);
            padding: 20px;
            text-align: center;
        }

        .path-body {
            padding: 20px;
        }

        .path-body h3 {
            margin-bottom: 15px;
            color: var(--text);
        }

        .path-meta {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .path-salary {
            font-weight: 700;
            color: var(--success);
            font-size: 1.2rem;
        }

        /* Skills Development */
        .skills {
            background-color: var(--white);
            padding: 60px 0;
            margin: 40px 0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .skill-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .skill-category {
            background-color: var(--light-gray);
            border-radius: 10px;
            padding: 25px;
        }

        .skill-category h3 {
            margin-bottom: 20px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .skill-list {
            list-style: none;
        }

        .skill-list li {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .skill-list li:last-child {
            border-bottom: none;
        }

        .skill-level {
            display: flex;
            gap: 5px;
        }

        .skill-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #e0e0e0;
        }

        .skill-dot.active {
            background-color: var(--primary);
        }

        /* Resources */
        .resources {
            margin: 60px 0;
        }

        .resource-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .resource-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            border-top: 4px solid var(--primary);
        }

        .resource-card:hover {
            transform: translateY(-10px);
        }

        .resource-icon {
            width: 60px;
            height: 60px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .resource-card h3 {
            margin-bottom: 15px;
            color: var(--text);
        }

        /* Newsletter */
        .newsletter {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: var(--white);
            padding: 60px 0;
            text-align: center;
            margin: 60px 0;
            border-radius: 10px;
        }

        .newsletter h2 {
            margin-bottom: 20px;
        }

        .newsletter p {
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 5px;
            overflow: hidden;
        }

        .newsletter-form input {
            flex: 1;
            padding: 15px;
            border: none;
            outline: none;
        }

        .newsletter-form button {
            background-color: var(--primary-dark);
            color: var(--white);
            border: none;
            padding: 0 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .newsletter-form button:hover {
            background-color: #0b3d91;
        }

        /* Footer */
        footer {
            background-color: var(--white);
            padding: 60px 0 30px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            margin-bottom: 20px;
            color: var(--primary);
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--primary);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            text-decoration: none;
            color: var(--text-light);
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background-color: var(--primary);
            color: var(--white);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #eee;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }

            nav ul {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .path-tabs {
                flex-direction: column;
                align-items: center;
            }

            .newsletter-form {
                flex-direction: column;
                border-radius: 5px;
            }

            .newsletter-form input {
                border-radius: 5px 5px 0 0;
            }

            .newsletter-form button {
                border-radius: 0 0 5px 5px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-road"></i>
                    <span>CareerPath</span>
                </div>
                <nav>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Assessment</a></li>
                        <li><a href="#">Career Paths</a></li>
                        <li><a href="#">Skills</a></li>
                        <li><a href="#">Resources</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <button class="btn btn-outline">Login</button>
                    <button class="btn btn-primary">Sign Up</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Find Your Perfect Career Path</h1>
                <p>Take our comprehensive assessment and discover career options that match your skills, interests, and goals.</p>
                <button class="btn btn-primary" style="padding: 15px 30px; font-size: 1.1rem;">Start Career Assessment</button>
            </div>
        </div>
    </section>

    <!-- Assessment Section -->
    <section class="container">
        <div class="assessment">
            <div class="section-title">
                <h2>Career Assessment Tools</h2>
            </div>
            <div class="assessment-cards">
                <div class="assessment-card">
                    <div class="assessment-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h3>Skills Assessment</h3>
                    <p>Evaluate your current skills and identify areas for improvement.</p>
                    <button class="btn btn-outline" style="margin-top: 15px;">Take Assessment</button>
                </div>
                <div class="assessment-card">
                    <div class="assessment-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>Personality Test</h3>
                    <p>Discover careers that match your personality type and work style.</p>
                    <button class="btn btn-outline" style="margin-top: 15px;">Take Assessment</button>
                </div>
                <div class="assessment-card">
                    <div class="assessment-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Career Interest Inventory</h3>
                    <p>Find careers that align with your interests and passions.</p>
                    <button class="btn btn-outline" style="margin-top: 15px;">Take Assessment</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Career Paths -->
    <section class="career-paths container">
        <div class="section-title">
            <h2>Explore Career Paths</h2>
        </div>
        <div class="path-tabs">
            <div class="path-tab active" data-tab="technology">Technology</div>
            <div class="path-tab" data-tab="business">Business</div>
            <div class="path-tab" data-tab="healthcare">Healthcare</div>
            <div class="path-tab" data-tab="creative">Creative</div>
        </div>

        <div class="path-content active" id="technology">
            <div class="path-grid">
                <div class="path-card">
                    <div class="path-header">
                        <h3>Software Developer</h3>
                    </div>
                    <div class="path-body">
                        <p>Design, develop, and maintain software applications and systems.</p>
                        <div class="path-meta">
                            <span><i class="far fa-clock"></i> 4-5 years</span>
                            <span><i class="far fa-user"></i> High Demand</span>
                        </div>
                        <div class="path-salary">$85,000 - $120,000</div>
                    </div>
                </div>
                <div class="path-card">
                    <div class="path-header">
                        <h3>Data Scientist</h3>
                    </div>
                    <div class="path-body">
                        <p>Analyze complex data to help organizations make informed decisions.</p>
                        <div class="path-meta">
                            <span><i class="far fa-clock"></i> 4-6 years</span>
                            <span><i class="far fa-user"></i> High Demand</span>
                        </div>
                        <div class="path-salary">$95,000 - $140,000</div>
                    </div>
                </div>
                <div class="path-card">
                    <div class="path-header">
                        <h3>Cybersecurity Analyst</h3>
                    </div>
                    <div class="path-body">
                        <p>Protect organizations from cyber threats and security breaches.</p>
                        <div class="path-meta">
                            <span><i class="far fa-clock"></i> 3-5 years</span>
                            <span><i class="far fa-user"></i> Growing Field</span>
                        </div>
                        <div class="path-salary">$75,000 - $110,000</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="path-content" id="business">
            <div class="path-grid">
                <div class="path-card">
                    <div class="path-header">
                        <h3>Marketing Manager</h3>
                    </div>
                    <div class="path-body">
                        <p>Plan and execute marketing strategies to promote products and services.</p>
                        <div class="path-meta">
                            <span><i class="far fa-clock"></i> 5-7 years</span>
                            <span><i class="far fa-user"></i> Medium Demand</span>
                        </div>
                        <div class="path-salary">$70,000 - $110,000</div>
                    </div>
                </div>
                <div class="path-card">
                    <div class="path-header">
                        <h3>Financial Analyst</h3>
                    </div>
                    <div class="path-body">
                        <p>Analyze financial data to guide business investment decisions.</p>
                        <div class="path-meta">
                            <span><i class="far fa-clock"></i> 3-5 years</span>
                            <span><i class="far fa-user"></i> Stable Field</span>
                        </div>
                        <div class="path-salary">$65,000 - $95,000</div>
                    </div>
                </div>
                <div class="path-card">
                    <div class="path-header">
                        <h3>HR Specialist</h3>
                    </div>
                    <div class="path-body">
                        <p>Manage recruitment, employee relations, and organizational development.</p>
                        <div class="path-meta">
                            <span><i class="far fa-clock"></i> 3-4 years</span>
                            <span><i class="far fa-user"></i> Steady Demand</span>
                        </div>
                        <div class="path-salary">$55,000 - $85,000</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Development -->
    <section class="container">
        <div class="skills">
            <div class="section-title">
                <h2>Essential Skills Development</h2>
            </div>
            <div class="skill-categories">
                <div class="skill-category">
                    <h3><i class="fas fa-laptop-code"></i> Technical Skills</h3>
                    <ul class="skill-list">
                        <li>Programming <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Data Analysis <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Cybersecurity <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Cloud Computing <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                    </ul>
                </div>
                <div class="skill-category">
                    <h3><i class="fas fa-users"></i> Soft Skills</h3>
                    <ul class="skill-list">
                        <li>Communication <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span></div></li>
                        <li>Leadership <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Problem Solving <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Teamwork <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span></div></li>
                    </ul>
                </div>
                <div class="skill-category">
                    <h3><i class="fas fa-graduation-cap"></i> Professional Development</h3>
                    <ul class="skill-list">
                        <li>Project Management <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Networking <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Public Speaking <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span><span class="skill-dot"></span></div></li>
                        <li>Time Management <div class="skill-level"><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot active"></span><span class="skill-dot"></span></div></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Resources -->
    <section class="resources container">
        <div class="section-title">
            <h2>Career Resources</h2>
        </div>
        <div class="resource-cards">
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3>Resume Builder</h3>
                <p>Create professional resumes tailored to your target industry and position.</p>
                <button class="btn btn-outline" style="margin-top: 15px;">Get Started</button>
            </div>
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Interview Prep</h3>
                <p>Practice common interview questions and improve your communication skills.</p>
                <button class="btn btn-outline" style="margin-top: 15px;">Practice Now</button>
            </div>
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h3>Networking Guide</h3>
                <p>Learn how to build professional connections that advance your career.</p>
                <button class="btn btn-outline" style="margin-top: 15px;">Learn More</button>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="container">
        <div class="newsletter">
            <h2>Stay Updated on Career Trends</h2>
            <p>Subscribe to our newsletter for the latest career advice, job market insights, and professional development tips.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Enter your email address">
                <button>Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CareerPath</h3>
                    <p>Your comprehensive guide to finding and succeeding in your ideal career.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Career Assessment</a></li>
                        <li><a href="#">Career Paths</a></li>
                        <li><a href="#">Skills Development</a></li>
                        <li><a href="#">Resources</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Career Fields</h3>
                    <ul class="footer-links">
                        <li><a href="#">Technology</a></li>
                        <li><a href="#">Business</a></li>
                        <li><a href="#">Healthcare</a></li>
                        <li><a href="#">Creative Arts</a></li>
                        <li><a href="#">Education</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Career Street, City</li>
                        <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                        <li><i class="fas fa-envelope"></i> info@careerpath.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 CareerPath. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Tab functionality for career paths
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.path-tab');
            const contents = document.querySelectorAll('.path-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>