import sys
import json
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Get input JSON
data = json.loads(sys.argv[1])

current = data['current']
others = data['others']

# Build corpus
corpus = [current] + [o['string'] for o in others]

# Vectorize
vectorizer = TfidfVectorizer().fit_transform(corpus)
vectors = vectorizer.toarray()

# Calculate similarities
cosine_sim = cosine_similarity([vectors[0]], vectors[1:])[0]

# Pair IDs with scores
similarities = list(zip([o['id'] for o in others], cosine_sim))

# Sort descending by similarity
similarities.sort(key=lambda x: x[1], reverse=True)

# Take top 20 IDs
top_ids = [str(pair[0]) for pair in similarities[:20]]

# Output result
print(json.dumps(top_ids))
