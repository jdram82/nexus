#!/bin/bash

# Portfolio templates
cat > portfolio-minimal.json << 'EOF'
{"name":"Minimal Portfolio","description":"Clean portfolio showcasing projects","category":"portfolio","type":"page","tags":["portfolio","minimal","creative"],"thumbnail":"portfolio-minimal.jpg","sections":[{"id":"intro","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Creative Designer & Developer","tag":"h1"}},{"type":"text","settings":{"content":"<p>Crafting beautiful digital experiences</p>"}}]}]},{"id":"projects","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"50%","widgets":[{"type":"heading","settings":{"text":"Project Alpha","tag":"h3"}}]},{"width":"50%","widgets":[{"type":"heading","settings":{"text":"Project Beta","tag":"h3"}}]}]}]}
EOF

cat > portfolio-photography.json << 'EOF'
{"name":"Photography Portfolio","description":"Stunning photography showcase","category":"portfolio","type":"page","tags":["portfolio","photography","gallery"],"thumbnail":"portfolio-photo.jpg","sections":[{"id":"gallery","type":"section","settings":{"padding":"0"},"columns":[{"width":"33.33%","widgets":[{"type":"image","settings":{"url":"/img/photo1.jpg","alt":"Photo 1"}}]},{"width":"33.33%","widgets":[{"type":"image","settings":{"url":"/img/photo2.jpg","alt":"Photo 2"}}]},{"width":"33.33%","widgets":[{"type":"image","settings":{"url":"/img/photo3.jpg","alt":"Photo 3"}}]}]}]}
EOF

# Blog templates
cat > blog-magazine.json << 'EOF'
{"name":"Magazine Blog","description":"Modern magazine-style blog layout","category":"blog","type":"page","tags":["blog","magazine","news"],"thumbnail":"blog-magazine.jpg","sections":[{"id":"featured","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Latest Articles","tag":"h2"}}]}]},{"id":"posts","type":"section","settings":{"padding":"40px 20px"},"columns":[{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Post Title 1","tag":"h3"}},{"type":"text","settings":{"content":"<p>Excerpt...</p>"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Post Title 2","tag":"h3"}},{"type":"text","settings":{"content":"<p>Excerpt...</p>"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Post Title 3","tag":"h3"}},{"type":"text","settings":{"content":"<p>Excerpt...</p>"}}]}]}]}
EOF

cat > blog-personal.json << 'EOF'
{"name":"Personal Blog","description":"Simple personal blog template","category":"blog","type":"page","tags":["blog","personal","simple"],"thumbnail":"blog-personal.jpg","sections":[{"id":"header","type":"section","settings":{"padding":"80px 20px","background":"#f9f9f9"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"My Personal Blog","tag":"h1"}},{"type":"text","settings":{"content":"<p>Thoughts and stories</p>"}}]}]},{"id":"posts","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Recent Posts","tag":"h2"}}]}]}]}
EOF

# Documentation templates
cat > docs-api.json << 'EOF'
{"name":"API Documentation","description":"Technical API documentation","category":"docs","type":"page","tags":["documentation","api","technical"],"thumbnail":"docs-api.jpg","sections":[{"id":"intro","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"API Reference","tag":"h1"}},{"type":"text","settings":{"content":"<p>Complete API documentation</p>"}}]}]},{"id":"endpoints","type":"section","settings":{"padding":"40px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Endpoints","tag":"h2"}},{"type":"text","settings":{"content":"<p>GET /api/v1/users</p>"}}]}]}]}
EOF

cat > docs-knowledge-base.json << 'EOF'
{"name":"Knowledge Base","description":"Help center and knowledge base","category":"docs","type":"page","tags":["documentation","help","support"],"thumbnail":"docs-kb.jpg","sections":[{"id":"search","type":"section","settings":{"padding":"80px 20px","background":"#667eea"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"How can we help?","tag":"h1","color":"#ffffff"}}]}]},{"id":"categories","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Getting Started","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Features","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Troubleshooting","tag":"h3"}}]}]}]}
EOF

# Landing page templates
cat > landing-product-launch.json << 'EOF'
{"name":"Product Launch","description":"High-converting product launch page","category":"landing","type":"landing","tags":["landing","product","launch"],"thumbnail":"landing-launch.jpg","sections":[{"id":"hero","type":"section","settings":{"padding":"100px 20px","background":"linear-gradient(135deg, #667eea 0%, #764ba2 100%)"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Revolutionary Product","tag":"h1","color":"#ffffff"}},{"type":"button","settings":{"text":"Pre-Order Now","url":"/order","style":"light"}}]}]},{"id":"features","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"50%","widgets":[{"type":"heading","settings":{"text":"Innovation","tag":"h3"}}]},{"width":"50%","widgets":[{"type":"heading","settings":{"text":"Performance","tag":"h3"}}]}]}]}
EOF

cat > landing-webinar.json << 'EOF'
{"name":"Webinar Registration","description":"Webinar registration landing page","category":"landing","type":"landing","tags":["landing","webinar","event"],"thumbnail":"landing-webinar.jpg","sections":[{"id":"hero","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Free Webinar: Master Your Skills","tag":"h1"}},{"type":"text","settings":{"content":"<p>Date: January 15, 2026</p>"}},{"type":"button","settings":{"text":"Register Now","url":"/register","style":"primary"}}]}]}]}
EOF

# Marketing/Agency templates
cat > marketing-agency.json << 'EOF'
{"name":"Marketing Agency","description":"Full-service marketing agency homepage","category":"marketing","type":"page","tags":["marketing","agency","services"],"thumbnail":"marketing-agency.jpg","sections":[{"id":"hero","type":"section","settings":{"padding":"100px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Grow Your Brand","tag":"h1"}},{"type":"text","settings":{"content":"<p>Data-driven marketing solutions</p>"}},{"type":"button","settings":{"text":"Get Started","url":"/contact","style":"primary"}}]}]},{"id":"services","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"SEO","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"PPC","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Social Media","tag":"h3"}}]}]}]}
EOF

cat > marketing-seo.json << 'EOF'
{"name":"SEO Services","description":"SEO services landing page","category":"marketing","type":"landing","tags":["marketing","seo","services"],"thumbnail":"marketing-seo.jpg","sections":[{"id":"hero","type":"section","settings":{"padding":"80px 20px","background":"#f5f5f5"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Rank Higher on Google","tag":"h1"}},{"type":"button","settings":{"text":"Free SEO Audit","url":"/audit","style":"primary"}}]}]},{"id":"benefits","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Traffic","tag":"h4"}}]},{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Rankings","tag":"h4"}}]},{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Leads","tag":"h4"}}]},{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Revenue","tag":"h4"}}]}]}]}
EOF

# Education templates
cat > education-course.json << 'EOF'
{"name":"Online Course","description":"Online course landing page","category":"education","type":"landing","tags":["education","course","learning"],"thumbnail":"education-course.jpg","sections":[{"id":"hero","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Master Web Development","tag":"h1"}},{"type":"text","settings":{"content":"<p>Complete course for beginners to advanced</p>"}},{"type":"button","settings":{"text":"Enroll Now","url":"/enroll","style":"primary"}}]}]},{"id":"curriculum","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Course Curriculum","tag":"h2"}}]}]}]}
EOF

cat > education-university.json << 'EOF'
{"name":"University Homepage","description":"Educational institution homepage","category":"education","type":"page","tags":["education","university","academic"],"thumbnail":"education-university.jpg","sections":[{"id":"hero","type":"section","settings":{"padding":"100px 20px","background":"#003366"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Welcome to Our University","tag":"h1","color":"#ffffff"}}]}]},{"id":"programs","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Undergraduate","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Graduate","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Research","tag":"h3"}}]}]}]}
EOF

# Events templates
cat > event-conference.json << 'EOF'
{"name":"Conference 2026","description":"Tech conference event page","category":"events","type":"page","tags":["events","conference","tech"],"thumbnail":"event-conference.jpg","sections":[{"id":"hero","type":"section","settings":{"padding":"120px 20px","background":"linear-gradient(135deg, #667eea 0%, #764ba2 100%)"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"TechConf 2026","tag":"h1","color":"#ffffff"}},{"type":"text","settings":{"content":"<p style='color:#fff'>March 15-17, 2026 | San Francisco</p>"}},{"type":"button","settings":{"text":"Register Now","url":"/register","style":"light"}}]}]},{"id":"speakers","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Featured Speakers","tag":"h2"}}]}]}]}
EOF

cat > event-meetup.json << 'EOF'
{"name":"Meetup Event","description":"Community meetup event page","category":"events","type":"page","tags":["events","meetup","community"],"thumbnail":"event-meetup.jpg","sections":[{"id":"details","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Monthly Developer Meetup","tag":"h1"}},{"type":"text","settings":{"content":"<p>Join us for an evening of learning and networking</p>"}},{"type":"button","settings":{"text":"RSVP","url":"/rsvp","style":"primary"}}]}]}]}
EOF

echo "Generated 12+ templates"
