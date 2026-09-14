#!/usr/bin/env python
# -*- coding: utf-8 -*-
import os

file_path = 'c:\\xampp\\htdocs\\CAPSTONE SQUIFM\\resources\\views\\inventory.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('<html>₱<head>₱<title>', '<html><head><title>')
text = text.replace('</head>₱<body>', '</head><body>')
text = text.replace('</body>₱</html>', '</body></html>')
text = text.replace('<tr>₱<td', '<tr><td')
text = text.replace('</td>₱</tr>', '</td></tr>')
text = text.replace('</tbody>₱</table>₱</div>', '</tbody></table></div>')
text = text.replace('</span>₱<br>', '</span><br>')
text = text.replace('₱<strong>', '<strong>')
text = text.replace('</strong>₱', '</strong>')
text = text.replace('>₱</script>', '></script>')
text = text.replace('>₱</canvas>', '></canvas>')
text = text.replace('<br>₱<small', '<br><small')
text = text.replace(' opacity: 0.3;">₱</div>', ' opacity: 0.3;"></div>')
text = text.replace('</span>₱</td>', '</span></td>')
text = text.replace('<td style="padding: 1rem; text-align: right;">₱<span', '<td style="padding: 1rem; text-align: right;"><span')
text = text.replace('<span class="category-icon">₱</span>', '<span class="category-icon"></span>')
text = text.replace('<div class="confirm-modal-icon" id="confirmModalIcon">₱</div>', '<div class="confirm-modal-icon" id="confirmModalIcon"></div>')
text = text.replace('<div class="confirm-modal-icon" id="updateModalIcon">₱</div>', '<div class="confirm-modal-icon" id="updateModalIcon"></div>')
text = text.replace('<strong>Temperature:</strong>₱<br>', '<strong>Temperature:</strong><br>')
text = text.replace('<strong>Humidity:</strong>₱<br>', '<strong>Humidity:</strong><br>')
text = text.replace('<span style="font-size: 1.25rem;">₱</span>', '<span style="font-size: 1.25rem;"></span>')
text = text.replace('<div style="font-size: 2rem; margin-bottom: 0.5rem;">₱</div>', '<div style="font-size: 2rem; margin-bottom: 0.5rem;"></div>')
text = text.replace('<span style="font-size: 1.5rem;">₱</span>', '<span style="font-size: 1.5rem;"></span>')
text = text.replace('</style>₱</head>₱<body>', '</style></head><body>')
text = text.replace('}}</span>₱</td>', '}}</span></td>')
text = text.replace('</small>₱</td>', '</small></td>')
text = text.replace('title="Edit Record">₱</button>', 'title="Edit Record"></button>')
text = text.replace('title="Delete Record">₱</button>', 'title="Delete Record"></button>')

text = text.replace('<html>₱<head>₱<title>', '<html><head><title>')
text = text.replace('</head>₱<body>', '</head><body>')

# Add missing ones from error lines
text = text.replace('opacity: 0.3;">₱</div>', 'opacity: 0.3;"></div>')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(text)

print('Done fixing emojis turned to peso.')