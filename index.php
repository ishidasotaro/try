<?php
// Simple single-page PHP app (HTML + JS) to demonstrate schedule aggregation workflow.
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スケジュール統合カレンダー</title>
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #0f172a;
            --muted: #f8fafc;
            --border: #e2e8f0;
        }

        * { box-sizing: border-box; }

        body {
            font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
            background: var(--muted);
            color: var(--secondary);
            margin: 0;
            padding: 0 0 48px;
        }

        header {
            background: linear-gradient(135deg, var(--primary), #1e3a8a);
            color: white;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        header h1 { margin: 0 0 8px; font-size: 28px; }
        header p { margin: 0; opacity: 0.95; }

        main {
            max-width: 1100px;
            margin: -32px auto 0;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15,23,42,0.08);
            padding: 20px 22px;
            margin-bottom: 18px;
            border: 1px solid var(--border);
        }

        .card h2 {
            margin: 0 0 10px;
            font-size: 20px;
            color: var(--secondary);
        }

        .flex {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        label { font-weight: 600; display: block; margin-bottom: 4px; }
        input, select, button, textarea {
            font: inherit;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        input, select, textarea {
            background: #fff;
            width: 100%;
        }

        button {
            background: var(--primary);
            color: white;
            cursor: pointer;
            border: none;
            font-weight: 700;
            transition: transform 0.05s ease, box-shadow 0.1s ease;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
        }
        button:hover { transform: translateY(-1px); }
        button:disabled { background: #cbd5e1; cursor: not-allowed; box-shadow: none; }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 18px;
        }

        .preview {
            width: 100%;
            border-radius: 10px;
            border: 1px dashed var(--border);
            background: #f8fafc;
            padding: 12px;
            min-height: 120px;
            display: grid;
            place-items: center;
            text-align: center;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-top: 16px;
        }

        .day-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
            background: #fff;
            min-height: 160px;
        }

        .day-card h3 {
            margin: 0 0 6px;
            font-size: 16px;
            color: var(--secondary);
        }

        .pill {
            display: inline-block;
            padding: 6px 8px;
            border-radius: 8px;
            background: #e0e7ff;
            color: #312e81;
            font-weight: 600;
            font-size: 13px;
            margin: 2px 0;
        }

        .event-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .event-item {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px;
            background: #f8fafc;
        }

        .availability {
            margin-top: 10px;
            border-top: 1px dashed var(--border);
            padding-top: 8px;
        }

        .availability span {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 6px 8px;
            border-radius: 8px;
            margin: 2px 4px 2px 0;
            font-weight: 600;
        }

        .muted { color: #64748b; }
    </style>
</head>
<body>
<header>
    <h1>スケジュール統合カレンダー</h1>
    <p>複数のカレンダーを一括取り込みし、予定の空き時間を週ごとに確認できます。</p>
</header>
<main>
    <section class="card">
        <h2>1. カレンダー投稿（スクリーンショット）</h2>
        <div class="section-grid">
            <div>
                <label for="calendarUpload">カレンダー画像（スクリーンショット）</label>
                <input type="file" id="calendarUpload" accept="image/*">
                <p class="muted" style="margin:6px 0 0;">※ 実際のAI認識ロジックは未実装です。右の「AI認識モック」を使うとサンプル予定を生成します。</p>
            </div>
            <div class="preview" id="uploadPreview">
                <span class="muted">アップロードした画像のプレビュー</span>
            </div>
        </div>
        <div style="margin-top:12px;" class="flex">
            <button id="mockRecognize">AI認識モックを実行</button>
            <button id="clearEvents" style="background:#0f172a; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.35);">予定をすべて削除</button>
        </div>
    </section>

    <section class="card">
        <h2>2. 予定の手動調整</h2>
        <div class="section-grid">
            <div>
                <label for="title">予定名</label>
                <input id="title" placeholder="打ち合わせ / 移動 / 会議 など">
            </div>
            <div>
                <label for="date">日付</label>
                <input id="date" type="date">
            </div>
            <div>
                <label for="start">開始時刻</label>
                <input id="start" type="time" value="09:00">
            </div>
            <div>
                <label for="end">終了時刻</label>
                <input id="end" type="time" value="10:00">
            </div>
        </div>
        <div style="margin-top:12px;">
            <button id="addEvent">予定を登録</button>
        </div>
    </section>

    <section class="card">
        <h2>3. 週次の予定と空き時間</h2>
        <p class="muted" style="margin:0 0 8px;">今週（本日を含む7日間）の予定一覧と空き時間を表示します。</p>
        <div id="calendar" class="calendar-grid"></div>
    </section>
</main>

<script>
const uploadInput = document.getElementById('calendarUpload');
const preview = document.getElementById('uploadPreview');
const mockButton = document.getElementById('mockRecognize');
const addEventButton = document.getElementById('addEvent');
const clearEventsButton = document.getElementById('clearEvents');
const calendarContainer = document.getElementById('calendar');

let events = [];

uploadInput.addEventListener('change', () => {
    const file = uploadInput.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.maxWidth = '100%';
        img.style.borderRadius = '8px';
        preview.innerHTML = '';
        preview.appendChild(img);
    };
    reader.readAsDataURL(file);
});

mockButton.addEventListener('click', () => {
    const baseDate = new Date();
    const sample = [
        { title: '営業定例', offset: 1, start: '10:00', end: '11:00' },
        { title: '社外MTG', offset: 2, start: '15:00', end: '16:30' },
        { title: '資料作成ブロック', offset: 4, start: '09:30', end: '12:00' },
        { title: '1on1', offset: 5, start: '13:00', end: '13:45' },
    ];
    sample.forEach(s => {
        const d = new Date(baseDate);
        d.setDate(d.getDate() + s.offset);
        events.push({
            id: crypto.randomUUID(),
            title: s.title,
            date: d.toISOString().slice(0,10),
            start: s.start,
            end: s.end,
            source: 'AIモック'
        });
    });
    renderCalendar();
});

addEventButton.addEventListener('click', () => {
    const title = document.getElementById('title').value.trim() || '予定';
    const date = document.getElementById('date').value;
    const start = document.getElementById('start').value;
    const end = document.getElementById('end').value;
    if (!date || !start || !end) {
        alert('日付と時間を入力してください');
        return;
    }
    if (start >= end) {
        alert('開始時刻は終了時刻より前にしてください');
        return;
    }
    events.push({
        id: crypto.randomUUID(),
        title,
        date,
        start,
        end,
        source: '手動登録'
    });
    renderCalendar();
});

clearEventsButton.addEventListener('click', () => {
    if (confirm('登録済みの予定をすべて削除しますか？')) {
        events = [];
        renderCalendar();
    }
});

function getCurrentWeek() {
    const today = new Date();
    const week = [];
    for (let i = 0; i < 7; i++) {
        const d = new Date(today);
        d.setDate(today.getDate() + i);
        week.push(d);
    }
    return week;
}

function formatDate(date) {
    const options = { month: 'numeric', day: 'numeric', weekday: 'short' };
    return date.toLocaleDateString('ja-JP', options);
}

function getEventsForDate(dateStr) {
    return events
        .filter(e => e.date === dateStr)
        .sort((a, b) => a.start.localeCompare(b.start));
}

function computeAvailability(dayEvents) {
    const workingStart = '08:00';
    const workingEnd = '20:00';
    const intervals = [{ start: workingStart, end: workingEnd }];
    dayEvents.forEach(ev => {
        const next = [];
        intervals.forEach(intv => {
            if (ev.end <= intv.start || ev.start >= intv.end) {
                next.push(intv);
                return;
            }
            if (ev.start > intv.start) {
                next.push({ start: intv.start, end: ev.start });
            }
            if (ev.end < intv.end) {
                next.push({ start: ev.end, end: intv.end });
            }
        });
        intervals.splice(0, intervals.length, ...next);
    });
    return intervals;
}

function renderCalendar() {
    calendarContainer.innerHTML = '';
    const week = getCurrentWeek();
    week.forEach(d => {
        const dateStr = d.toISOString().slice(0,10);
        const dayEvents = getEventsForDate(dateStr);
        const availability = computeAvailability(dayEvents);
        const card = document.createElement('div');
        card.className = 'day-card';
        const title = document.createElement('h3');
        title.textContent = `${formatDate(d)}`;
        card.appendChild(title);

        const pill = document.createElement('span');
        pill.className = 'pill';
        pill.textContent = dayEvents.length ? `${dayEvents.length}件の予定` : '予定なし';
        card.appendChild(pill);

        const list = document.createElement('div');
        list.className = 'event-list';
        dayEvents.forEach(ev => {
            const item = document.createElement('div');
            item.className = 'event-item';
            item.innerHTML = `<strong>${ev.title}</strong><br><span class="muted">${ev.start} - ${ev.end} | ${ev.source}</span>`;
            list.appendChild(item);
        });
        if (!dayEvents.length) {
            const empty = document.createElement('div');
            empty.className = 'muted';
            empty.textContent = '登録された予定はありません';
            list.appendChild(empty);
        }
        card.appendChild(list);

        const availabilityBlock = document.createElement('div');
        availabilityBlock.className = 'availability';
        const label = document.createElement('div');
        label.textContent = '空き時間 (08:00-20:00)';
        label.className = 'muted';
        availabilityBlock.appendChild(label);
        availability.forEach(intv => {
            const span = document.createElement('span');
            span.textContent = `${intv.start} - ${intv.end}`;
            availabilityBlock.appendChild(span);
        });
        card.appendChild(availabilityBlock);

        calendarContainer.appendChild(card);
    });
}

renderCalendar();
</script>
</body>
</html>
