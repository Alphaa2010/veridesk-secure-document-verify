document.addEventListener('DOMContentLoaded',()=>{
  if(!window.Chart)return;
  const colors=['#3e6ee8','#e2a83d','#28a274','#d75c62','#8793a3'];
  const configs=[['statusChart',['Under Review','Action Required','Completed','Submitted','Not Submitted'],[0,0,0,0,0]],['docChart',['Pending','Approved','Rejected','Resubmission Required'],[0,0,0,0]]];
  const groups=window.verideskCharts||{};
  configs.forEach(([id,labels])=>{const c=document.getElementById(id);const rows=id==='statusChart'?groups.applications:groups.documents;const mapped=new Map((rows||[]).map(r=>[r.status,Number(r.n)]));const values=labels.map(k=>mapped.get(k)||0);if(c)new Chart(c,{type:'doughnut',data:{labels,datasets:[{data:values,backgroundColor:colors,borderWidth:0}]},options:{responsive:true,cutout:'68%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,boxWidth:7,font:{size:11}}}}}})});
});
