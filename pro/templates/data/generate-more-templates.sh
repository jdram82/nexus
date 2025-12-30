#!/bin/bash

# More business templates
for i in 1 2 3 4 5; do
  cat > business-corporate-$i.json << EOF
{"name":"Corporate Layout $i","description":"Professional corporate page","category":"business","type":"page","tags":["business","corporate"],"sections":[{"id":"hero","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Corporate Excellence","tag":"h1"}}]}]}]}
EOF
done

# More SaaS templates
for i in 1 2 3 4 5; do
  cat > saas-product-$i.json << EOF
{"name":"SaaS Product Page $i","description":"Product showcase page","category":"saas","type":"page","tags":["saas","product"],"sections":[{"id":"hero","type":"section","settings":{"padding":"100px 20px","background":"#667eea"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Innovative SaaS Solution","tag":"h1","color":"#ffffff"}}]}]}]}
EOF
done

# More e-commerce templates
for i in 1 2 3 4; do
  cat > ecommerce-product-$i.json << EOF
{"name":"Product Catalog $i","description":"Product catalog page","category":"ecommerce","type":"page","tags":["ecommerce","catalog"],"sections":[{"id":"products","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Product","tag":"h3"}}]},{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Product","tag":"h3"}}]},{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Product","tag":"h3"}}]},{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Product","tag":"h3"}}]}]}]}
EOF
done

# More portfolio templates
for i in 1 2 3 4; do
  cat > portfolio-creative-$i.json << EOF
{"name":"Creative Portfolio $i","description":"Creative showcase","category":"portfolio","type":"page","tags":["portfolio","creative"],"sections":[{"id":"work","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"50%","widgets":[{"type":"heading","settings":{"text":"Featured Work","tag":"h2"}}]},{"width":"50%","widgets":[{"type":"text","settings":{"content":"<p>Portfolio description</p>"}}]}]}]}
EOF
done

# More blog templates
for i in 1 2 3; do
  cat > blog-tech-$i.json << EOF
{"name":"Tech Blog $i","description":"Technology blog layout","category":"blog","type":"page","tags":["blog","tech"],"sections":[{"id":"posts","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"66.67%","widgets":[{"type":"heading","settings":{"text":"Latest Posts","tag":"h2"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Sidebar","tag":"h3"}}]}]}]}
EOF
done

# More documentation templates
for i in 1 2 3; do
  cat > docs-guide-$i.json << EOF
{"name":"Documentation Guide $i","description":"User guide template","category":"docs","type":"page","tags":["docs","guide"],"sections":[{"id":"content","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"25%","widgets":[{"type":"heading","settings":{"text":"Table of Contents","tag":"h3"}}]},{"width":"75%","widgets":[{"type":"heading","settings":{"text":"Guide Content","tag":"h2"}}]}]}]}
EOF
done

# More landing pages
for i in 1 2 3 4 5; do
  cat > landing-conversion-$i.json << EOF
{"name":"High Convert Landing $i","description":"Conversion-optimized page","category":"landing","type":"landing","tags":["landing","conversion"],"sections":[{"id":"hero","type":"section","settings":{"padding":"100px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Convert More Visitors","tag":"h1"}},{"type":"button","settings":{"text":"Get Started","url":"#","style":"primary"}}]}]}]}
EOF
done

# More marketing templates
for i in 1 2 3; do
  cat > marketing-service-$i.json << EOF
{"name":"Marketing Service $i","description":"Service page template","category":"marketing","type":"page","tags":["marketing","service"],"sections":[{"id":"service","type":"section","settings":{"padding":"80px 20px"},"columns":[{"width":"50%","widgets":[{"type":"heading","settings":{"text":"Our Service","tag":"h2"}}]},{"width":"50%","widgets":[{"type":"text","settings":{"content":"<p>Service details</p>"}}]}]}]}
EOF
done

# More education templates
for i in 1 2 3; do
  cat > education-learning-$i.json << EOF
{"name":"Learning Platform $i","description":"E-learning platform page","category":"education","type":"page","tags":["education","learning"],"sections":[{"id":"courses","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Course Category","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Course Category","tag":"h3"}}]},{"width":"33.33%","widgets":[{"type":"heading","settings":{"text":"Course Category","tag":"h3"}}]}]}]}
EOF
done

# More event templates
for i in 1 2; do
  cat > event-workshop-$i.json << EOF
{"name":"Workshop Event $i","description":"Workshop event page","category":"events","type":"page","tags":["events","workshop"],"sections":[{"id":"details","type":"section","settings":{"padding":"60px 20px"},"columns":[{"width":"100%","widgets":[{"type":"heading","settings":{"text":"Workshop Details","tag":"h1"}},{"type":"button","settings":{"text":"Register","url":"#","style":"primary"}}]}]}]}
EOF
done

echo "Generated additional templates"
