{{-- TAB: Sessions Calendar --}}
<div id="ctab-sessions" style="display:none;">
@php
    $sessionsJs = $sessions->map(fn($s) => [
        'id'       => $s->id,
        'title'    => $s->title_ar ?: 'جلسة',
        'subject'  => $s->subject->name_ar ?? null,
        'teacher'  => $s->teacher->name ?? null,
        'at'       => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->format('Y-m-d H:i:s') : null,
        'duration' => $s->duration_minutes,
        'status'   => $s->status,
        'number'   => $s->session_number,
    ])->filter(fn($s) => $s['at'])->values();
@endphp
<div style="margin-top:24px;background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:15px;font-weight:700;color:#111827;">الجدول والجلسات</span>
            <span style="background:#eef2ff;color:#4338ca;border-radius:9999px;padding:.12rem .55rem;font-size:.68rem;font-weight:700;">{{ $sessions->count() }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <button @click="sessionModal = true"
                    style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إنشاء جلسات
            </button>
            @if($sessions->isNotEmpty())
            <button type="button" onclick="clearAllSessions()"
                    style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:#fff1f2;color:#dc2626;border:1px solid #fecaca;font-size:12px;font-weight:700;cursor:pointer;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                مسح الجلسات
            </button>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:12px 20px;border-bottom:1px solid #f1f5f9;background:white;">
        <input id="calSearch" type="text" placeholder="بحث بالعنوان..." oninput="calApplyFilters()"
               style="flex:1;min-width:160px;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:12px;font-family:inherit;">
        @if($class->program && $class->program->type === 'diploma')
        <select id="calSubject" onchange="calApplyFilters()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:12px;font-family:inherit;background:white;">
            <option value="">كل المقررات</option>
            @foreach($classSubjects as $cs)
                <option value="{{ $cs->name_ar ?: $cs->name_en }}">{{ $cs->name_ar ?: $cs->name_en }}</option>
            @endforeach
        </select>
        @endif
        <select id="calStatus" onchange="calApplyFilters()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:12px;font-family:inherit;background:white;">
            <option value="">كل الحالات</option>
            <option value="scheduled">مجدولة</option>
            <option value="live">مباشرة</option>
            <option value="completed">مكتملة</option>
            <option value="cancelled">ملغاة</option>
        </select>
        <button type="button" onclick="calResetFilters()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;background:white;cursor:pointer;color:#64748b;font-size:12px;font-weight:600;font-family:inherit;">إعادة تعيين</button>
        <span id="calFilterCount" style="font-size:11px;color:#94a3b8;"></span>
    </div>

    {{-- Nav --}}
    <div id="calNav" style="display:flex;align-items:center;justify-content:center;gap:16px;padding:14px;border-bottom:1px solid #f1f5f9;background:white;">
        <button type="button" onclick="calPrev()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8249;</button>
        <h3 id="calTitle" style="font-size:16px;font-weight:700;color:#111827;margin:0;min-width:200px;text-align:center;"></h3>
        <button type="button" onclick="calNext()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8250;</button>
        <button type="button" onclick="calToday()" style="padding:8px 14px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#0071AA;font-size:12px;font-weight:700;font-family:inherit;">اليوم</button>
    </div>

    <div id="calBody"></div>
</div>

<script>
(function(){
    const SESSIONS = {!! $sessionsJs->toJson() !!};
    const TODAY = new Date();
    const DAY_NAMES = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    const MONTH_NAMES = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    const STATUS = {
        scheduled:['#dbeafe','#1d4ed8','مجدولة'],
        live:['#fee2e2','#dc2626','● مباشر'],
        completed:['#dcfce7','#15803d','مكتملة'],
        cancelled:['#fee2e2','#dc2626','ملغاة'],
    };
    let view = 'week';
    let cur  = new Date();
    let filters = { q: '', subject: '', status: '' };

    const parse = at => new Date(at.replace(' ','T'));
    const fmtTime = d => { let h=d.getHours(), m=String(d.getMinutes()).padStart(2,'0'); let ap=h>=12?'م':'ص'; let hh=h%12||12; return hh+':'+m+' '+ap; };
    const sameDay = (a,b)=> a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();
    const st = s => STATUS[s] || ['#f1f5f9','#64748b',s];

    function filtered(){
        return SESSIONS.filter(s => {
            if (filters.q && !((s.title||'').toLowerCase().includes(filters.q.toLowerCase()))) return false;
            if (filters.subject && (s.subject||'') !== filters.subject) return false;
            if (filters.status && (s.status||'') !== filters.status) return false;
            return true;
        });
    }
    const onDay = date => filtered().filter(s=>sameDay(parse(s.at),date)).sort((a,b)=>parse(a.at)-parse(b.at));

    window.calApplyFilters = function(){
        filters.q       = document.getElementById('calSearch')?.value || '';
        filters.subject = document.getElementById('calSubject')?.value || '';
        filters.status  = document.getElementById('calStatus')?.value || '';
        render();
    };
    window.calResetFilters = function(){
        filters = { q:'', subject:'', status:'' };
        ['calSearch','calSubject','calStatus'].forEach(id=>{ const el=document.getElementById(id); if(el) el.value=''; });
        render();
    };
    window.calSetView = function(v){ view=v; document.getElementById('calNav').style.display='flex'; render(); };
    window.calPrev  = function(){ if(view==='week') cur.setDate(cur.getDate()-7); else cur.setMonth(cur.getMonth()-1); render(); };
    window.calNext  = function(){ if(view==='week') cur.setDate(cur.getDate()+7); else cur.setMonth(cur.getMonth()+1); render(); };
    window.calToday = function(){ cur = new Date(); render(); };

    function renderList(){
        const data = filtered();
        if(!SESSIONS.length) return `<div style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد جلسات لهذه المجموعة بعد</div>`;
        if(!data.length)     return `<div style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد جلسات مطابقة للفلتر</div>`;
        const rows = [...data].sort((a,b)=>parse(a.at)-parse(b.at)).map((s,i)=>{
            const d=parse(s.at); const c=st(s.status);
            const date = d.toLocaleDateString('ar-EG',{year:'numeric',month:'2-digit',day:'2-digit'});
            return `<tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:12px 16px;color:#cbd5e1;font-size:11px;">${s.number ?? i+1}</td>
                <td style="padding:12px 16px;font-weight:600;color:#1e293b;">${s.title}</td>
                <td style="padding:12px 16px;color:#475569;" dir="ltr">${date} · ${fmtTime(d)}</td>
                <td style="padding:12px 16px;color:#6366f1;font-weight:600;">${s.teacher || '—'}</td>
                <td style="padding:12px 16px;text-align:center;color:#64748b;">${s.duration} د</td>
                <td style="padding:12px 16px;text-align:center;"><span style="background:${c[0]};color:${c[1]};border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">${c[2]}</span></td>
            </tr>`;
        }).join('');
        return `<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="border-bottom:2px solid #f1f5f9;background:#fafafa;">
                <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:40px;">#</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">العنوان</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الموعد</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">المعلم</th>
                <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:70px;">المدة</th>
                <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:100px;">الحالة</th>
            </tr></thead><tbody>${rows}</tbody></table></div>`;
    }

    function weekStart(){ const d=new Date(cur); d.setDate(d.getDate()-d.getDay()); d.setHours(0,0,0,0); return d; }

    function renderWeek(){
        const ws=weekStart();
        const wend=new Date(ws.getFullYear(),ws.getMonth(),ws.getDate()+6);
        document.getElementById('calTitle').textContent = ws.getDate()+' '+MONTH_NAMES[ws.getMonth()]+' — '+wend.getDate()+' '+MONTH_NAMES[wend.getMonth()];

        const weekDays = [];
        for(let i=0;i<5;i++){ const d=new Date(ws); d.setDate(d.getDate()+i); weekDays.push(d); }

        const t = (h,m)=> h*60+m;
        const PERIODS = [
            { s:t(8,10),  e:t(9,20),  range:'9:20 - 8:10',   name:'الفترة الصباحية (1)' },
            { s:t(9,30),  e:t(10,40), range:'10:40 - 9:30',  name:'الفترة الصباحية (2)' },
            { s:t(10,50), e:t(12,0),  range:'12:00 - 10:50', name:'الفترة الصباحية (3)' },
            { s:t(12,20), e:t(13,25), range:'1:25 - 12:20',  name:'الفترة المسائية (1)' },
            { s:t(13,35), e:t(14,40), range:'2:40 - 1:35',   name:'الفترة المسائية (2)' },
            { s:t(14,50), e:t(15,55), range:'3:55 - 2:50',   name:'الفترة المسائية (3)' },
            { s:t(16,0),  e:t(17,15), range:'5:15 - 4:00',   name:'الفترة المسائية (4)' },
        ];
        const periodIndex = mins => { for(let p=0;p<PERIODS.length;p++) if(mins<PERIODS[p].e) return p; return PERIODS.length-1; };

        const cellMap = {};
        weekDays.forEach((d,i)=>{
            onDay(d).forEach(s=>{
                const dt=parse(s.at); const mins=dt.getHours()*60+dt.getMinutes();
                const p=periodIndex(mins);
                (cellMap[i+'|'+p]=cellMap[i+'|'+p]||[]).push(s);
            });
        });

        let head = `<th style="padding:10px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;width:80px;">اليوم<br><span style="font-size:10px;opacity:.85;">الفترة</span></th>`;
        PERIODS.forEach(p=>{
            head += `<th style="padding:8px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;line-height:1.6;">${p.name}<br><bdi dir="ltr" style="font-size:11px;font-weight:600;opacity:.9;display:inline-block;unicode-bidi:isolate;">${p.range}</bdi></th>`;
        });

        let body='';
        weekDays.forEach((d,i)=>{
            const today=sameDay(d,TODAY);
            let row=`<td style="padding:10px 6px;text-align:center;font-size:13px;font-weight:700;color:#fff;background:${today?'#005a88':'#0071AA'};border:1px solid #fff;line-height:1.4;">${DAY_NAMES[i]}</td>`;
            PERIODS.forEach((p,pi)=>{
                const items=cellMap[i+'|'+pi]||[];
                const inner=items.map(s=>{
                    const showSub=s.subject&&!(s.title||'').includes(s.subject);
                    return `<div style="background:#eff6ff;border-right:3px solid #0071AA;border-radius:6px;padding:6px 8px;margin-bottom:4px;line-height:1.35;">
                        <div style="font-size:12px;font-weight:700;color:#1e3a8a;">${s.title}</div>
                        ${showSub?`<div style="font-size:10px;color:#64748b;">${s.subject}</div>`:''}
                        ${s.teacher?`<div style="font-size:10px;color:#6366f1;font-weight:600;margin-top:2px;">👤 ${s.teacher}</div>`:''}
                        <div style="display:flex;gap:4px;justify-content:flex-start;margin-top:5px;">
                            <button type="button" onclick="rescheduleSession(${s.id},'${s.at}')" title="تعديل الموعد" style="width:20px;height:20px;border:none;border-radius:5px;background:#dbeafe;color:#1d4ed8;cursor:pointer;font-size:10px;line-height:1;">✎</button>
                            <button type="button" onclick="deleteSession(${s.id})" title="حذف" style="width:20px;height:20px;border:none;border-radius:5px;background:#fee2e2;color:#dc2626;cursor:pointer;font-size:11px;line-height:1;">🗑</button>
                        </div>
                    </div>`;
                }).join('');
                row+=`<td style="height:80px;padding:5px;vertical-align:top;border:1px solid #d6e4f0;${today?'background:#f8fdff;':''}">${inner}</td>`;
            });
            body+=`<tr>${row}</tr>`;
        });

        return `<div style="padding:14px;overflow-x:auto;"><table style="width:100%;min-width:1000px;border-collapse:collapse;table-layout:fixed;"><thead><tr>${head}</tr></thead><tbody>${body}</tbody></table></div>`;
    }

    function renderMonth(){
        const y=cur.getFullYear(), m=cur.getMonth();
        document.getElementById('calTitle').textContent = MONTH_NAMES[m]+' '+y;
        const firstDow=new Date(y,m,1).getDay(), dim=new Date(y,m+1,0).getDate(), prevDim=new Date(y,m,0).getDate();
        let cells=[];
        for(let i=firstDow-1;i>=0;i--) cells.push({d:prevDim-i,cur:false});
        for(let d=1;d<=dim;d++) cells.push({d,cur:true});
        let fill=42-cells.length; for(let d=1;d<=fill;d++) cells.push({d,cur:false});
        const headers=DAY_NAMES.map(d=>`<div style="padding:10px 4px;text-align:center;font-size:11px;font-weight:700;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;">${d}</div>`).join('');
        const grid=cells.map(cell=>{
            if(!cell.cur) return `<div style="min-height:104px;padding:6px;border-left:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;background:#fafafa;"><span style="font-size:13px;color:#d1d5db;">${cell.d}</span></div>`;
            const date=new Date(y,m,cell.d), isToday=sameDay(date,TODAY), items=onDay(date);
            const chips=items.slice(0,3).map(s=>`<div style="background:#eff6ff;color:#1e3a8a;border-right:2px solid #0071AA;font-size:10px;font-weight:600;padding:2px 5px;border-radius:3px;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${fmtTime(parse(s.at))} ${s.title}</div>`).join('');
            const more=items.length-3>0?`<div style="font-size:10px;color:#7c3aed;font-weight:700;">+${items.length-3} أخرى</div>`:'';
            const num=isToday?`display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;background:#0071AA;color:white;border-radius:50%;font-weight:700;font-size:13px;`:`font-size:13px;font-weight:500;color:#374151;`;
            return `<div style="min-height:104px;padding:6px;border-left:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;background:${isToday?'#eff6ff':'white'};"><div style="margin-bottom:4px;text-align:left;"><span style="${num}">${cell.d}</span></div>${chips}${more}</div>`;
        }).join('');
        return `<div style="display:grid;grid-template-columns:repeat(7,1fr);">${headers}</div><div style="display:grid;grid-template-columns:repeat(7,1fr);">${grid}</div>`;
    }

    function render(){
        const body=document.getElementById('calBody');
        if(view==='list') body.innerHTML=renderList();
        else if(view==='week') body.innerHTML=renderWeek();
        else body.innerHTML=renderMonth();
        const cntEl=document.getElementById('calFilterCount');
        if(cntEl){ const n=filtered().length, total=SESSIONS.length; cntEl.textContent=(n===total)?`${total} جلسة`:`${n} من ${total}`; }
    }
    render();
})();

const _SESS_CSRF = '{{ csrf_token() }}';
window.rescheduleSession = function(id, currentAt){
    document.getElementById('editSessId').value   = id;
    document.getElementById('editSessDate').value = (currentAt||'').slice(0,10);
    const timeSel=document.getElementById('editSessTime'), timePart=(currentAt||'').slice(11,16);
    timeSel.value=timePart; if(timeSel.value!==timePart) timeSel.value='';
    document.getElementById('editSessModal').style.display='flex';
};
window.closeEditSession = function(){ document.getElementById('editSessModal').style.display='none'; };
window.submitEditSession = function(){
    const id=document.getElementById('editSessId').value, date=document.getElementById('editSessDate').value, time=document.getElementById('editSessTime').value;
    if(!date||!time){ alert('اختر التاريخ والفترة'); return; }
    fetch(`/admin/sessions/${id}/reschedule`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':_SESS_CSRF},body:JSON.stringify({scheduled_at:date+' '+time+':00'})})
        .then(r=>r.json()).then(d=>{ if(d.success){ location.hash='#sessions'; location.reload(); } else alert(d.message||'تعذّر التعديل'); });
};
window.clearAllSessions = function(){
    if(!confirm('سيتم حذف كل جلسات هذه المجموعة. هل أنت متأكد؟')) return;
    fetch(`/admin/classes/{{ $class->id }}/sessions`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':_SESS_CSRF},body:JSON.stringify({_method:'DELETE'})})
        .then(r=>r.json().catch(()=>({success:r.ok}))).then(d=>{ if(d.success!==false){ location.hash='#sessions'; location.reload(); } else alert('تعذّر الحذف'); });
};
window.deleteSession = function(id){
    if(!confirm('حذف هذه الجلسة؟')) return;
    fetch(`/admin/sessions/${id}`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':_SESS_CSRF},body:JSON.stringify({_method:'DELETE'})})
        .then(r=>r.json().catch(()=>({success:r.ok}))).then(d=>{ if(d.success!==false){ location.hash='#sessions'; location.reload(); } else alert('تعذّر الحذف'); });
};
</script>
</div>{{-- /ctab-sessions --}}
