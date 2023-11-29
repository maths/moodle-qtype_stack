# Example mathematical proofs

This directory contains example mathematical proofs and other arguments as samples and test cases for the proof library.

Each file must define three variables:

1. a variable `thm` to hold a statement of the theorem.  This is a string variable.
2. a variable `proof_steps`, which is a list of `["key", "proof string"]` pairs.
3. a variable `proof_ans` which represents the proof.

A teacher refers to the individual steps in the proof using 

1. the short `"key"` string.  
2. the integer position in the proof.

For example, if we have "\(\log_2(3)\) is irrational." then the teacher can define the proof as a list of steps using keys as follows.

    proof_ans:proof("assume","defn_rat","defn_rat2","defn_log","defn_log2","alg","alg_int","contra","conc");

The function `proof` represents a proof.  Rather than using a list, which has no type information, using the function `proof` signals that this represents a proof.  The use of keys, e.g. `"assume"` is more meaningful than numbered steps and it also allows steps to be inserted without re-numbering the steps in a proof.

For example, if we have theorem  "\(n\) is odd if and only if \(n^2\) is odd", then we define its proof as a tree using

    proof_ans:proof_iff(proof(1,2,3,4,5),proof(6,7,8,9,10,11));

Really this means the proof is an if and only if proof (`proof_iff`) with two blocks, themselves proofs.  The sub-proofs are the most basic proof type, which is a list of steps, e.g. `proof(1,2,3,4,5)`.   The steps of a proof are integer indexes to the `proof_steps` list.  We deal with indexes, not strings, to simplify the representation and manipulation of the proof trees.
