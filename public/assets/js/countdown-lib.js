let Countdown=function(){let e;const t=(e,t,s,o,n,a,c)=>{let i="",y="",r="",d="",u="";""!==e?(i=jQuery(e).find(".met-sales-months"),y=jQuery(e).find(".met-sales-days"),r=jQuery(e).find(".met-sales-hours"),d=jQuery(e).find(".met-sales-minutes"),u=jQuery(e).find(".met-sales-seconds")):(i=jQuery(".met-sales-months"),y=jQuery(".met-sales-days"),r=jQuery(".met-sales-hours"),d=jQuery(".met-sales-minutes"),u=jQuery(".met-sales-seconds"));let x=jQuery(".label-month"),p=jQuery(".label-day"),h=jQuery(".label-hour"),f=jQuery(".label-minute"),M=jQuery(".label-second"),b=jQuery(".month-block"),j=jQuery(".day-block"),Q=jQuery(".hour-block"),m=jQuery(".minute-block");jQuery(".second-block");"tillMonth"==t?(i.text(l(s)),y.text(l(o)),r.text(l(n)),d.text(l(a)),u.text(l(c)),b.css("display","block"),b.next().css("display","block"),j.css("display","block"),j.next().css("display","block"),Q.css("display","block"),Q.next().css("display","block"),x.text("Months"),p.text("Days"),h.text("Hours"),f.text("Minutes"),M.text("Seconds")):"tillDay"==t?(y.text(l(o)),r.text(l(n)),d.text(l(a)),u.text(l(c)),b.css("display","none"),b.next().css("display","none"),j.css("display","block"),Q.css("display","block"),j.next().css("display","block"),Q.next().css("display","block"),p.text("Days"),h.text("Hours"),f.text("Minutes"),M.text("Seconds")):"tillHour"==t?(r.text(l(n)),d.text(l(a)),u.text(l(c)),b.css("display","none"),b.next().css("display","none"),j.css("display","none"),j.next().css("display","none"),Q.css("display","block"),Q.next().css("display","block"),h.text("Hours"),f.text("Minutes"),M.text("Seconds")):"tillMin"==t?(d.text(l(a)),u.text(l(c)),b.css("display","none"),b.next().css("display","none"),j.css("display","none"),j.next().css("display","none"),Q.css("display","none"),Q.next().css("display","none"),f.text("Minutes"),M.text("Seconds")):"tillSec"==t&&(M.text("Seconds"),b.css("display","none"),j.css("display","none"),m.css("display","none"),Q.css("display","none"),u.text(l(c)))};function s(s,l,o,n){let a=l-new Date;if(a<=0)""!==o?jQuery(o).remove():jQuery(".met-sales-countdown-wrapper").remove(),clearInterval(e);else{let e=a/1e3,s="",l="",c="",i="",y="";"tillMonth"==n?(s=Math.floor(e%60),l=Math.floor(e/60%60),c=Math.floor(e/60/60%24),i=Math.floor(e/3600/24%30),y=Math.floor(e/2592e3)):"tillDay"==n?(s=Math.floor(e%60),l=Math.floor(e/60%60),c=Math.floor(e/60/60%24),i=Math.floor(e/3600/24)):"tillHour"==n?(s=Math.floor(e%60),l=Math.floor(e/60%60),c=Math.floor(e/60/60)):"tillMin"==n?(s=Math.floor(e%60),l=Math.floor(e/60)):"tillSec"==n&&(s=Math.floor(e)),t(o,n,y,i,c,l,s)}}const l=e=>e<10?`0${e}`:e;return{init:function(t,l,o,n){new Date(t);let a=new Date(l),c=o,i=n;""==c&&clearInterval(e),s(0,a,c,i),e=setInterval((()=>{s(0,a,c,i)}),1e3)}}}();