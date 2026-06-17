{{-- Weekly schedule calendar — shared by dashboard & my-sessions. Expects $classSessions. --}}
@php $allCalSessions = $classSessions ?? collect(); @endphp
    <style>
    @keyframes calLivePulseStu {
        0%,100% { box-shadow:0 0 0 2px rgba(220,38,38,.25); }
        50%     { box-shadow:0 0 0 4px rgba(220,38,38,.45); }
    }
    .cal-live-card-stu { animation: calLivePulseStu 1.6s ease-in-out infinite; }
    </style>
    <div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
            <span style="font-size:15px;font-weight:700;color:#111827;">الجدول الأسبوعي</span>
            <div style="display:flex;align-items:center;gap:10px;">
                <button type="button" onclick="calPrev()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8249;</button>
                <h3 id="calTitle" style="font-size:16px;font-weight:700;color:#111827;margin:0;min-width:220px;text-align:center;"></h3>
                <button type="button" onclick="calNext()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8250;</button>
                <button type="button" onclick="calToday()" style="padding:8px 14px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#0071AA;font-size:12px;font-weight:700;">اليوم</button>
            </div>
        </div>
        <div id="calBody" style="overflow-x:auto;"></div>
    </div>

    {{-- Session detail modal --}}
    <div id="sessionModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:16px;">
        <div style="background:white;border-radius:20px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
            <div style="padding:18px 20px;background:linear-gradient(135deg,#0f172a,#0071AA);display:flex;align-items:center;justify-content:space-between;">
                <h3 id="smTitle" style="color:white;font-size:15px;font-weight:700;margin:0;"></h3>
                <button onclick="document.getElementById('sessionModal').style.display='none'" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:28px;height:28px;color:white;cursor:pointer;font-size:16px;">×</button>
            </div>
            <div id="smBody" style="padding:18px;"></div>
        </div>
    </div>

    {{-- Apology modal --}}
    <div id="apologyModal" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:16px;direction:rtl;">
        <div style="background:white;border-radius:20px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
            <div style="padding:18px 20px;background:linear-gradient(135deg,#c2410c,#ea580c);display:flex;align-items:center;justify-content:space-between;">
                <h3 style="color:white;font-size:15px;font-weight:700;margin:0;">📝 تقديم عذر غياب</h3>
                <button type="button" onclick="closeApology()" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:28px;height:28px;color:white;cursor:pointer;font-size:16px;">×</button>
            </div>
            <form id="apoForm" method="POST" enctype="multipart/form-data" style="padding:18px 20px;">
                @csrf
                <p style="font-size:13px;color:#64748b;margin:0 0 12px;">المحاضرة: <strong id="apoSessionTitle" style="color:#1e293b;"></strong></p>

                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">سبب الغياب <span style="color:#dc2626;">*</span></label>
                <textarea id="apoReason" name="reason" rows="4" required minlength="5" maxlength="1000"
                          placeholder="اكتب سبب غيابك عن المحاضرة..."
                          style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 12px;font-size:13px;box-sizing:border-box;resize:vertical;outline:none;"></textarea>

                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin:12px 0 6px;">مرفق (اختياري) <span style="font-weight:400;color:#94a3b8;">— PDF أو صورة</span></label>
                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                       style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:8px;font-size:12px;box-sizing:border-box;">

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;">
                    <button type="button" onclick="closeApology()" style="padding:9px 16px;border-radius:10px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:13px;font-weight:600;cursor:pointer;">إلغاء</button>
                    <button type="submit" style="padding:9px 20px;border-radius:10px;border:none;background:linear-gradient(135deg,#c2410c,#ea580c);color:#fff;font-size:13px;font-weight:700;cursor:pointer;">إرسال العذر</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const CAL_SESSIONS = @json($allCalSessions->values());
    const TODAY_CAL = new Date();
    // Default to week of next upcoming session if current week has no sessions
    let curCal = (function(){
        const now = new Date();
        const ws = new Date(now); ws.setDate(ws.getDate()-ws.getDay()); ws.setHours(0,0,0,0);
        const we = new Date(ws); we.setDate(we.getDate()+6); we.setHours(23,59,59,999);
        const hasThisWeek = CAL_SESSIONS.some(s=>{ const d=new Date(s.scheduled_at); return d>=ws&&d<=we; });
        if(!hasThisWeek){
            const future = CAL_SESSIONS.filter(s=>s.scheduled_at&&new Date(s.scheduled_at)>now).sort((a,b)=>new Date(a.scheduled_at)-new Date(b.scheduled_at));
            if(future.length) return new Date(future[0].scheduled_at);
        }
        return now;
    })();

    const DAY_NAMES_CAL   = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    const MONTH_NAMES_CAL = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

    const tMin = (h,m) => h*60+m;
    const PERIODS_CAL = [
        { e:tMin(9,20),  range:'9:20 - 8:10',   name:'الفترة الصباحية (1)' },
        { e:tMin(10,40), range:'10:40 - 9:30',  name:'الفترة الصباحية (2)' },
        { e:tMin(12,0),  range:'12:00 - 10:50', name:'الفترة الصباحية (3)' },
        { e:tMin(13,25), range:'1:25 - 12:20',  name:'الفترة المسائية (1)' },
        { e:tMin(14,40), range:'2:40 - 1:35',   name:'الفترة المسائية (2)' },
        { e:tMin(15,55), range:'3:55 - 2:50',   name:'الفترة المسائية (3)' },
        { e:tMin(17,15), range:'5:15 - 4:00',   name:'الفترة المسائية (4)' },
    ];
    const getPeriod = mins => { for(let p=0;p<PERIODS_CAL.length;p++) if(mins<PERIODS_CAL[p].e) return p; return PERIODS_CAL.length-1; };

    function sameDayCal(a,b){ return a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
    // A session is "live now" if flagged live, or now falls within its scheduled window
    function isSessionLiveCal(s){
        if(s.status==='live') return true;
        if(s.status==='completed') return false;
        if(!s.scheduled_at) return false;
        const st=new Date(s.scheduled_at), en=new Date(st.getTime()+(s.duration_minutes||60)*60000), n=new Date();
        return n>=st && n<=en;
    }
    function fmtTimeCal(iso){ const d=new Date(iso); let h=d.getHours(),m=String(d.getMinutes()).padStart(2,'0'); return (h%12||12)+':'+m+(h<12?' ص':' م'); }
    function typeStyleCal(type){
        if(type==='live_zoom')      return {bg:'#dbeafe',color:'#1d4ed8',label:''};
        if(type==='in_person')      return {bg:'#dcfce7',color:'#15803d',label:'حضوري'};
        if(type==='recorded_video') return {bg:'#fce7f3',color:'#be185d',label:'مسجّل'};
        return {bg:'#f3f4f6',color:'#4b5563',label:''};
    }
    function weekStartCal(d){ const c=new Date(d); c.setDate(c.getDate()-c.getDay()); c.setHours(0,0,0,0); return c; }
    function sessionsOnDayCal(date){ return CAL_SESSIONS.filter(s=>s.scheduled_at&&sameDayCal(new Date(s.scheduled_at),date)).sort((a,b)=>new Date(a.scheduled_at)-new Date(b.scheduled_at)); }

    function openSessionCal(s){
        document.getElementById('smTitle').textContent = s.title||('جلسة #'+s.session_number);
        document.getElementById('sessionModal').style.display='flex';
        const ts=typeStyleCal(s.type);
        const statusBg    = s.status==='completed'?'#dcfce7':s.status==='live'?'#fee2e2':'#dbeafe';
        const statusColor = s.status==='completed'?'#15803d':s.status==='live'?'#dc2626':'#1d4ed8';
        const statusLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'';
        const rows=[
            s.scheduled_at?['📅 الموعد', new Date(s.scheduled_at).toLocaleDateString('ar-SA',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' — '+fmtTimeCal(s.scheduled_at)]:null,
            s.duration_minutes?['⏱ المدة', s.duration_minutes+' دقيقة']:null,
            s.subject_name?['📚 المادة', s.subject_name]:null,
            s.teacher_name?['👤 المدرب', s.teacher_name]:null,
            ts.label?['🔖 النوع', ts.label]:null,
            statusLabel?['📊 الحالة', statusLabel]:null,
            (s.attended!==null && (s.ended_at || (s.scheduled_at && new Date(s.scheduled_at)<new Date())))?['✅ الحضور', s.attended?'حضرت':'غائب']:null,
        ].filter(Boolean);
        let html='<div style="display:flex;flex-direction:column;gap:10px;">';
        rows.forEach(([k,v])=>{
            html+=`<div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#f8fafc;border-radius:9px;">
                <span style="font-size:12px;color:#64748b;min-width:90px;">${k}</span>
                <span style="font-size:13px;font-weight:600;color:#1e293b;">${v}</span>
            </div>`;
        });
        const zoomIconStu = `<svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>`;
        // Always show two stacked join buttons. Both record attendance via join-zoom.
        const hasAnyLink = (s.zoom_start_url||s.zoom_join_url);
        if(hasAnyLink && s.status!=='completed'){
            // Join 1 — start link
            html+=`<a href="/student/sessions/${s.id}/join-zoom?link=start" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;margin-top:4px;">
                ${zoomIconStu} ▶ انضمام للجلسة 1
            </a>`;
            // Join 2 — student join link
            html+=`<a href="/student/sessions/${s.id}/join-zoom?link=join" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;margin-top:4px;">
                ${zoomIconStu} ▶ انضمام للجلسة 2
            </a>`;
        }
        if(s.status==='completed'){
            html+=`<div style="text-align:center;padding:8px;background:#f0fdf4;border-radius:10px;font-size:13px;font-weight:600;color:#15803d;">✓ انتهت هذه الجلسة</div>`;
        }
        // Absence apology — available for any session the student didn't attend
        // (past absences, or future sessions the student knows they'll miss).
        // Hidden only when already marked present, or the session is live right now.
        const isLiveNow = s.status==='live' || (s.scheduled_at && (function(){
            const st=new Date(s.scheduled_at), en=new Date(st.getTime()+(s.duration_minutes||60)*60000);
            const n=new Date(); return n>=st && n<=en;
        })());
        if(s.attended!==true && !isLiveNow){
            if(s.apology_status==='pending'){
                html+=`<div style="text-align:center;padding:9px;background:#fef9c3;border-radius:10px;font-size:12px;font-weight:700;color:#a16207;margin-top:4px;">⏳ عذرك قيد المراجعة</div>`;
            } else if(s.apology_status==='approved'){
                html+=`<div style="text-align:center;padding:9px;background:#dcfce7;border-radius:10px;font-size:12px;font-weight:700;color:#15803d;margin-top:4px;">✓ تم قبول عذر غيابك (معذور)</div>`;
            } else if(s.apology_status==='rejected'){
                html+=`<div style="text-align:center;padding:9px;background:#fee2e2;border-radius:10px;font-size:12px;font-weight:700;color:#b91c1c;margin-top:4px;">✗ تم رفض عذر غيابك</div>`;
            } else {
                html+=`<button type="button" onclick="openApology(${s.id}, ${JSON.stringify(s.title||'').replace(/"/g,'&quot;')})" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#fff7ed;color:#c2410c;border:1.5px solid #fed7aa;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;margin-top:4px;width:100%;">📝 تقديم عذر غياب</button>`;
            }
        }
        html+='</div>';
        document.getElementById('smBody').innerHTML=html;
    }

    function openApology(sessionId, title){
        document.getElementById('sessionModal').style.display='none';
        document.getElementById('apoSessionTitle').textContent = title || '';
        document.getElementById('apoForm').action = '/student/sessions/'+sessionId+'/apology';
        document.getElementById('apoReason').value='';
        document.getElementById('apologyModal').style.display='flex';
    }
    function closeApology(){ document.getElementById('apologyModal').style.display='none'; }

    function renderCal(){
        const ws=weekStartCal(curCal);
        const wend=new Date(ws.getFullYear(),ws.getMonth(),ws.getDate()+6);
        document.getElementById('calTitle').textContent=ws.getDate()+' '+MONTH_NAMES_CAL[ws.getMonth()]+' — '+wend.getDate()+' '+MONTH_NAMES_CAL[wend.getMonth()]+' '+wend.getFullYear();

        const weekDays=[];
        for(let i=0;i<5;i++){ const d=new Date(ws); d.setDate(d.getDate()+i); weekDays.push(d); }

        const cellMap={};
        weekDays.forEach((d,i)=>{
            sessionsOnDayCal(d).forEach(s=>{
                const dt=new Date(s.scheduled_at); const mins=dt.getHours()*60+dt.getMinutes();
                const p=getPeriod(mins);
                (cellMap[i+'|'+p]=cellMap[i+'|'+p]||[]).push(s);
            });
        });

        let head=`<th style="padding:10px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;width:80px;">اليوم<br><span style="font-size:10px;opacity:.85;">الفترة</span></th>`;
        PERIODS_CAL.forEach(p=>{
            head+=`<th style="padding:8px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;line-height:1.6;">
                ${p.name}<br><bdi dir="ltr" style="font-size:11px;font-weight:600;opacity:.9;display:inline-block;unicode-bidi:isolate;">${p.range}</bdi>
            </th>`;
        });

        let body='';
        weekDays.forEach((d,i)=>{
            const isToday=sameDayCal(d,TODAY_CAL);
            let row=`<td style="padding:10px 6px;text-align:center;font-size:13px;font-weight:700;color:#fff;background:${isToday?'#005a88':'#0071AA'};border:1px solid #fff;line-height:1.4;">
                ${DAY_NAMES_CAL[i]}<br><span style="font-size:11px;font-weight:500;opacity:.85;">${d.getDate()} ${MONTH_NAMES_CAL[d.getMonth()]}</span>
            </td>`;
            PERIODS_CAL.forEach((p,pi)=>{
                const items=cellMap[i+'|'+pi]||[];
                const inner=items.map(s=>{
                    const ts=typeStyleCal(s.type);
                    const isLive = isSessionLiveCal(s);
                    const statusBg    = s.status==='completed'?'#dcfce7':isLive?'#fee2e2':'#eff6ff';
                    const statusColor = s.status==='completed'?'#15803d':isLive?'#dc2626':'#1e3a8a';
                    const statusLabel = s.status==='completed'?'مكتملة':isLive?'● مباشر الآن':'';
                    const showSub = s.subject_name && !(s.title||'').includes(s.subject_name);
                    const sessionPast = s.ended_at || (s.scheduled_at && new Date(s.scheduled_at) < new Date());
                    const attendedBadge = (sessionPast && !isLive)
                        ? (s.attended===true?`<span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">حضرت</span>`:s.attended===false?`<span style="background:#fee2e2;color:#dc2626;font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">غائب</span>`:'')
                        : '';
                    const cardStyle = isLive
                        ? 'background:linear-gradient(135deg,#fef2f2,#fee2e2);border-right:4px solid #dc2626;box-shadow:0 0 0 2px rgba(220,38,38,.25);'
                        : 'background:#eff6ff;border-right:3px solid #0071AA;';
                    return `<div onclick='openSessionCal(${JSON.stringify(s)})' class="${isLive?'cal-live-card-stu':''}" style="${cardStyle}border-radius:6px;padding:6px 8px;margin-bottom:4px;line-height:1.35;cursor:pointer;">
                        <div style="font-size:12px;font-weight:700;color:${isLive?'#991b1b':'#1e3a8a'};">${s.title||s.subject_name||'جلسة'}</div>
                        ${showSub?`<div style="font-size:10px;color:#64748b;">${s.subject_name}</div>`:''}
                        ${s.teacher_name?`<div style="font-size:10px;color:#64748b;">👤 ${s.teacher_name}</div>`:''}
                        <div style="display:flex;gap:4px;flex-wrap:wrap;margin-top:4px;">
                            ${ts.label?`<span style="background:${ts.bg};color:${ts.color};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${ts.label}</span>`:''}
                            ${statusLabel?`<span style="background:${statusBg};color:${statusColor};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${statusLabel}</span>`:''}
                            ${attendedBadge}
                            ${(s.status==='live'||s.status==='scheduled')&&s.zoom_join_url
                                ?`<a href="/student/sessions/${s.id}/join-zoom" target="_blank" onclick="event.stopPropagation()" style="background:#2563eb;color:white;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;text-decoration:none;">📹 انضم</a>`
                                :''}
                        </div>
                    </div>`;
                }).join('');
                row+=`<td style="min-height:80px;padding:5px;vertical-align:top;border:1px solid #d6e4f0;${isToday?'background:#f8fdff;':''}">${inner}</td>`;
            });
            body+=`<tr>${row}</tr>`;
        });

        document.getElementById('calBody').innerHTML=`
            <table style="width:100%;min-width:1000px;border-collapse:collapse;table-layout:fixed;">
                <thead><tr>${head}</tr></thead>
                <tbody>${body}</tbody>
            </table>`;
    }

    function calPrev(){ curCal.setDate(curCal.getDate()-7); renderCal(); }
    function calNext(){ curCal.setDate(curCal.getDate()+7); renderCal(); }
    function calToday(){ curCal=new Date(); renderCal(); }

    renderCal();

    </script>
