You can change the number of columns of videos under the main video by changing the width of the videos through adding some extra CSS to the theme customizer:<br><br>
<strong>For one column:</strong><br>
.pyw_poster {<br>
width: 100%;<br>
margin:<br>
}<br>
<br>
<strong>For three columns:</strong><br>
@media only screen and (min-width: BREAKPOINT) {<br>
.pyw_poster {<br>
width: 31%;<br>
margin-right: 3%;<br>
}<br>
<br>
div.pyw_poster:nth-child(3n+1) {<br>
margin-right: 0;<br>
}<br>
}<br>
<br>
<strong>For four, five or six columns, change the values in the code above according to the values in the table below:</strong><br>
| columns | width | margin-right | :nthchild value |<br>
| 4 | 23% | 2.75% | 4n+1<br>
| 5 | 19% | 1% | 5n+1<br>
| 6 | 15% | 2% | 6n+1