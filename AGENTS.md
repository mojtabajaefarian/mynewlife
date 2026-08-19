# 🤖 Agents Protocol

## ورود هر Agent جدید

1. بخوان: `.ai-memory/BRAIN.md`
2. بخوان: `.ai-memory/agents/_orchestrator.md`
3. اعلام کن: نقش و وظیفه‌ات چیست

## Agent های فعال

| Agent          | مدل پیشنهادی | مسئولیت     |
| -------------- | ------------ | ----------- |
| Orchestrator   | Claude/Opus  | هماهنگی     |
| Backend Coder  | DeepSeek     | PHP         |
| Frontend Coder | Gemini       | JS/CSS      |
| Test Agent     | Qwen         | تست         |
| Code Reviewer  | Claude       | بازبینی     |
| GitHub Agent   | هر مدل       | commit/push |
| Coach Agent    | Claude       | اصول کوچینگ |

## قوانین بین Agent ها

- هر Agent فقط در حوزه خودش تغییر می‌دهد
- هیچ Agent ای بدون تایید Test Agent کد را به کاربر نمی‌دهد
- GitHub Agent فقط با کامنت استاندارد push می‌کند
