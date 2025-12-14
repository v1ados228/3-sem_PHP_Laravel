<template>
    <div v-if="article != null" class="alert alert-primary alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
         role="alert" style="z-index: 9999; max-width: 600px;">
        <strong>Новая статья добавлена!</strong><br>
        <a :href="`/article/${article.id}`" class="alert-link">{{ article.title }}</a>
        <button type="button" class="btn-close" @click="closeAlert" aria-label="Close"></button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            article: null
        }
    },
    created() {
        console.log('App.vue component created');
        console.log('Echo available:', typeof window.Echo !== 'undefined');
        
        if (typeof window.Echo === 'undefined') {
            console.error('Laravel Echo is not initialized!');
            return;
        }
        
        console.log('Subscribing to channel: articles');
        const channel = window.Echo.channel('articles');
        
        console.log('Listening for event: NewArticleEvent');
        channel.listen('NewArticleEvent', (data) => {
            console.log('Received new article event:', data);
            
            // Проверяем, не является ли текущий пользователь создателем статьи
            // Если да, то не показываем уведомление (он и так знает, что создал статью)
            // Можно добавить проверку через meta тег или data атрибут
            const currentUserId = document.querySelector('meta[name="user-id"]')?.content;
            const articleAuthorId = data.article?.author_id;
            
            // Показываем уведомление только если пользователь не создатель статьи
            // или если мы не можем определить ID пользователя
            if (!currentUserId || currentUserId !== String(articleAuthorId)) {
                this.article = data.article;
                
                // Автоматически скрыть уведомление через 10 секунд
                setTimeout(() => {
                    this.article = null;
                }, 10000);
            } else {
                console.log('Article created by current user, skipping notification');
            }
        });
        
        // Также слушаем стандартное имя события (без broadcastAs)
        channel.listen('.NewArticleEvent', (data) => {
            console.log('Received new article event (with dot):', data);
            
            const currentUserId = document.querySelector('meta[name="user-id"]')?.content;
            const articleAuthorId = data.article?.author_id;
            
            if (!currentUserId || currentUserId !== String(articleAuthorId)) {
                this.article = data.article;
                
                setTimeout(() => {
                    this.article = null;
                }, 10000);
            } else {
                console.log('Article created by current user, skipping notification');
            }
        });
    },
    methods: {
        closeAlert() {
            this.article = null;
        }
    }
}
</script>

<style scoped>
.alert {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        transform: translate(-50%, -100%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}
</style>

